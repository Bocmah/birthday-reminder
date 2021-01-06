<?php

declare(strict_types=1);

namespace Tests\Unit\Observee;

use DateTimeImmutable;
use Exception;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use Tests\TestCaseWithPromisesHelpers;
use Vkbd\Observee\Observee;
use Vkbd\Observee\ObserveeAlreadyExists;
use Vkbd\Observee\ObserveeId;
use Vkbd\Observee\ObserveeStorage;
use Vkbd\Observee\ObserveeWasNotFound;
use Vkbd\Observer\ObserverId;
use Vkbd\Person\FullName;
use Vkbd\Vk\User\NumericVkId;

use function React\Promise\resolve;
use function Tests\await;

final class ObserveeStorageTest extends TestCaseWithPromisesHelpers
{
    public function test_it_checks_if_observee_already_exists(): void
    {
        $observerId = new ObserverId(5);
        $vkId = new NumericVkId(10560);
        $fullName = new FullName('John', 'Doe');
        $birthdate = new DateTimeImmutable('13.10.1996');

        $queryResult = new QueryResult();
        $queryResult->resultRows = [1];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection
            ->method('query')
            ->willReturn(resolve($queryResult));

        $storage = new ObserveeStorage($connection);

        $this->assertRejectsWith(
            $storage->create(
                $observerId,
                $vkId,
                $fullName,
                $birthdate
            ),
            ObserveeAlreadyExists::class,
        );
    }

    /**
     * @throws Exception
     */
    public function test_it_passes_insert_statement_to_connection(): void
    {
        $observerId = new ObserverId(5);
        $vkId = new NumericVkId(25);
        $fullName = new FullName('John', 'Doe');
        $birthdate = new DateTimeImmutable('10.05.1990');

        $observeeId = 1;
        $insertQueryResult = new QueryResult();
        $insertQueryResult->insertId = $observeeId;

        $connection = $this->createMock(ConnectionInterface::class);
        $connection
            ->method('query')
            ->willReturnOnConsecutiveCalls(
                resolve(new QueryResult()),
                resolve($insertQueryResult)
            );

        $insert = 'INSERT INTO observees (observer_id, first_name, last_name, vk_id, birthdate) VALUES (?, ?, ?, ?, ?)';

        $connection
            ->expects(self::exactly(2))
            ->method('query')
            ->withConsecutive(
                [
                    'SELECT 1 FROM observees WHERE observer_id = ? AND vk_id = ?',
                    [$observerId->value(), $vkId->value()],
                ],
                [$insert, [$observerId->value(), $fullName->firstName(), $fullName->lastName(), $vkId->value(), '1990-05-10']],
            );

        $storage = new ObserveeStorage($connection);

        await($storage->create(
            $observerId,
            $vkId,
            $fullName,
            $birthdate
        ));
    }

    /**
     * @throws Exception
     */
    public function test_it_gets_observee_by_observer_id_and_vk_id(): void
    {
        $rawObserverId = 10;
        $observerId = new ObserverId($rawObserverId);
        $rawVkId = 824703;
        $vkId = new NumericVkId($rawVkId);
        $rawObservee = [
            'id' => '1',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'vk_id' => (string) $rawVkId,
            'birthdate' => '1990-05-16',
            'observer_id' => (string) $rawObserverId,
        ];
        $queryResult = new QueryResult();
        $queryResult->resultRows = [$rawObservee];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection
            ->method('query')
            ->willReturn(resolve($queryResult));

        $connection
            ->expects(self::once())
            ->method('query')
            ->with(
                self::stringContains('SELECT'),
                self::equalTo([$observerId->value(), $vkId->value()])
            );

        $storage = new ObserveeStorage($connection);

        $this->assertResolvesWith(
            $storage->findByObserverIdAndVkId($observerId, $vkId),
            new Observee(
                new ObserveeId((int) $rawObservee['id']),
                $vkId,
                new FullName($rawObservee['first_name'], $rawObservee['last_name']),
                new DateTimeImmutable($rawObservee['birthdate']),
                $observerId,
            )
        );
    }

    public function test_it_rejects_when_observee_was_not_found(): void
    {
        $queryResult = new QueryResult();
        $queryResult->resultRows = [];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection
            ->method('query')
            ->willReturn(resolve($queryResult));

        $storage = new ObserveeStorage($connection);

        $this->assertRejectsWith(
            $storage->findByObserverIdAndVkId(new ObserverId(10), new NumericVkId(5)),
            ObserveeWasNotFound::class,
        );
    }
}
