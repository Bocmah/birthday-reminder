<?php

declare(strict_types=1);

namespace Vkbd\Observee;

use DateTimeImmutable;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use React\Promise\PromiseInterface;
use Vkbd\Observer\ObserverId;
use Vkbd\Person\FullName;
use Vkbd\Vk\User\Id\NumericVkId;

use function React\Promise\reject;
use function React\Promise\resolve;

class ObserveeStorage
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param ObserverId $observerId
     * @param NumericVkId $vkId
     *
     * @return PromiseInterface<Observee>
     */
    public function findByObserverIdAndVkId(ObserverId $observerId, NumericVkId $vkId): PromiseInterface
    {
        $columns = 'id, first_name, last_name, vk_id, birthdate, observer_id';

        return $this->connection
            ->query(
                "SELECT $columns FROM observees WHERE observer_id = ? AND vk_id = ?",
                [$observerId->value(), $vkId->value()]
            )
            ->then(static function (QueryResult $result) use ($observerId, $vkId) {
                if (empty($result->resultRows)) {
                    throw ObserveeWasNotFound::withObserverIdAndVkId($observerId, $vkId);
                }

                /** @var array{
                 *      id: string,
                 *      vk_id: string,
                 *      first_name: string,
                 *      last_name: string,
                 *      birthdate: string,
                 *      observer_id: string
                 * } $row
                 */
                $row = $result->resultRows[0];

                return new Observee(
                    new ObserveeId((int) $row['id']),
                    new NumericVkId((int) $row['vk_id']),
                    new FullName($row['first_name'], $row['last_name']),
                    new DateTimeImmutable($row['birthdate']),
                    new ObserverId((int) $row['observer_id']),
                );
            });
    }

    /**
     * @param ObserverId $observerId
     * @param NumericVkId $vkId
     * @param FullName $fullName
     * @param DateTimeImmutable $birthdate
     *
     * @return PromiseInterface<QueryResult>
     */
    public function create(
        ObserverId $observerId,
        NumericVkId $vkId,
        FullName $fullName,
        DateTimeImmutable $birthdate
    ): PromiseInterface {
        return $this
            ->observeeDoesNotExist($observerId, $vkId)
            ->then(fn () => $this->connection->query(
                'INSERT INTO observees (observer_id, first_name, last_name, vk_id, birthdate) VALUES (?, ?, ?, ?, ?)',
                [$observerId->value(), $fullName->firstName(), $fullName->lastName(), $vkId->value(), $birthdate->format('Y-m-d')],
            ));
    }

    private function observeeDoesNotExist(ObserverId $observerId, NumericVkId $vkId): PromiseInterface
    {
        return $this->connection
            ->query(
                'SELECT 1 FROM observees WHERE observer_id = ? AND vk_id = ?',
                [$observerId->value(), $vkId->value()]
            )
            ->then(
                static fn (QueryResult $result): PromiseInterface => empty($result->resultRows) ?
                    resolve() :
                    reject(new ObserveeAlreadyExists())
            );
    }
}
