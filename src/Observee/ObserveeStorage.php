<?php

declare(strict_types=1);

namespace Vkbd\Observee;

use DateTimeImmutable;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use React\Promise\PromiseInterface;
use Vkbd\Observer\ObserverId;
use Vkbd\Person\FullName;
use Vkbd\Vk\NumericVkId;

use function React\Promise\reject;
use function React\Promise\resolve;

final class ObserveeStorage
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function create(
        ObserverId $observerId,
        NumericVkId $vkId,
        FullName $fullName,
        DateTimeImmutable $birthdate
    ): PromiseInterface {
        return $this
            ->observeeDoesNotExist($observerId, $vkId)
            ->then(fn () => $this->connection->query(
                // phpcs:disable Generic.Files.LineLength.TooLong
                'INSERT INTO observees (observer_id, first_name, last_name, vk_id, birthdate) VALUES (?, ?, ?, ?, ?)',
                [$observerId->id(), $fullName->firstName(), $fullName->lastName(), $vkId->id(), $birthdate->format('Y-m-d')],
                // phpcs:enable
            ));
    }

    private function observeeDoesNotExist(ObserverId $observerId, NumericVkId $vkId): PromiseInterface
    {
        return $this->connection
            ->query(
                'SELECT 1 FROM observees WHERE observer_id = ? AND vk_id = ?',
                [$observerId->id(), $vkId->id()]
            )
            ->then(
                fn (QueryResult $result): PromiseInterface => empty($result->resultRows) ?
                    resolve() :
                    reject(new ObserveeAlreadyExists())
            );
    }
}
