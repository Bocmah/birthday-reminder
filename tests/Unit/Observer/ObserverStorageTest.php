<?php

declare(strict_types=1);

namespace Tests\Unit\Observer;

use Exception;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use Tests\TestCaseWithPromisesHelpers;
use Vkbd\Observer\Observer;
use Vkbd\Observer\ObserverId;
use Vkbd\Observer\ObserverStorage;
use Vkbd\Observer\ObserverWasNotFound;
use Vkbd\Person\FullName;
use Vkbd\Vk\User\Id\NumericVkId;

use function React\Promise\resolve;

final class ObserverStorageTest extends TestCaseWithPromisesHelpers
{
    public function test_it_checks_if_observer_already_exists(): void
    {
        $vkId = new NumericVkId(25);
        $fullName = new FullName('John', 'Doe');
        $queryResult = new QueryResult();
        $queryResult->resultRows = [1];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection
            ->method('query')
            ->willReturn(resolve($queryResult));

        $storage = new ObserverStorage($connection);

        $this->assertRejectsWithExceptionMessage(
            $storage->create($vkId, $fullName),
            'Observer already exists',
        );
    }

    /**
     * @throws Exception
     */
    public function test_it_passes_insert_statement_to_connection_and_returns_created_observer(): void
    {
        $vkId = new NumericVkId(25);
        $fullName = new FullName('John', 'Doe');

        $observerId = 1;
        $insertQueryResult = new QueryResult();
        $insertQueryResult->insertId = $observerId;

        $connection = $this->createMock(ConnectionInterface::class);
        $connection
            ->method('query')
            ->willReturnOnConsecutiveCalls(
                resolve(new QueryResult()),
                resolve($insertQueryResult)
            );

        $insert = 'INSERT INTO observers (first_name, last_name, vk_id, should_always_be_notified) VALUES (?, ?, ?, ?)';

        $connection
            ->expects(self::exactly(2))
            ->method('query')
            ->withConsecutive(
                ['SELECT 1 FROM observers WHERE vk_id = ?', [$vkId->value()]],
                [$insert, [$fullName->firstName(), $fullName->lastName(), $vkId->value(), true]],
            );

        $storage = new ObserverStorage($connection);

        $this->assertResolvesWith(
            $storage->create($vkId, $fullName),
            new Observer(new ObserverId($observerId), $vkId, $fullName)
        );
    }

    /**
     * @dataProvider invalidInsertIds
     *
     * @param $insertId
     * @param string $expectedExceptionMessage
     */
    public function test_it_rejects_on_receiving_invalid_insert_id(?int $insertId, string $expectedExceptionMessage): void
    {
        $vkId = new NumericVkId(25);
        $fullName = new FullName('John', 'Doe');

        $insertQueryResult = new QueryResult();
        $insertQueryResult->insertId = $insertId;

        $connection = $this->createMock(ConnectionInterface::class);
        $connection
            ->method('query')
            ->willReturnOnConsecutiveCalls(
                resolve(new QueryResult()),
                resolve($insertQueryResult)
            );

        $storage = new ObserverStorage($connection);

        $this->assertRejectsWithExceptionMessage(
            $storage->create($vkId, $fullName),
            $expectedExceptionMessage,
        );
    }

    public function test_it_gets_observer_by_vk_id(): void
    {
        $rawVkId = 824703;
        $vkId = new NumericVkId($rawVkId);
        $rawObserver = [
            'id' => '1',
            'vk_id' => (string) $rawVkId,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'should_always_be_notified' => '1',
        ];
        $queryResult = new QueryResult();
        $queryResult->resultRows = [$rawObserver];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection
            ->method('query')
            ->willReturn(resolve($queryResult));

        $connection
            ->expects(self::once())
            ->method('query')
            ->with(
                self::stringContains('SELECT'),
                self::equalTo([$vkId->value()])
            );

        $storage = new ObserverStorage($connection);

        $this->assertResolvesWith(
            $storage->findByVkId($vkId),
            new Observer(
                new ObserverId((int) $rawObserver['id']),
                $vkId,
                new FullName($rawObserver['first_name'], $rawObserver['last_name']),
                true,
            ),
        );
    }

    public function test_it_rejects_when_observer_was_not_found(): void
    {
        $queryResult = new QueryResult();
        $queryResult->resultRows = [];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection
            ->method('query')
            ->willReturn(resolve($queryResult));

        $storage = new ObserverStorage($connection);

        $this->assertRejectsWith(
            $storage->findByVkId(new NumericVkId(5)),
            ObserverWasNotFound::class,
        );
    }

    /**
     * @return iterable<array>
     */
    public function invalidInsertIds(): iterable
    {
        return [
            [null, 'Received null insert id'],
            [-1, 'Received non-positive insert id'],
            [0, 'Received non-positive insert id'],
        ];
    }
}
