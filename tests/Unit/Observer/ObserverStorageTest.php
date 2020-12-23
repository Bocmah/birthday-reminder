<?php

declare(strict_types=1);

namespace Tests\Unit\Observer;

use Exception;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use Vkbd\Observer\Observer;
use Vkbd\Observer\ObserverAlreadyExists;
use Vkbd\Observer\ObserverId;
use Vkbd\Observer\ObserverStorage;
use Vkbd\Observer\ObserverWasNotFound;
use Vkbd\Person\FullName;
use Vkbd\Vk\NumericVkId;

use function Clue\React\Block\await;
use function React\Promise\resolve;

final class ObserverStorageTest extends TestCase
{
    /** @noinspection BadExceptionsProcessingInspection */
    /**
     * @throws Exception
     */
    public function test_it_checks_if_observer_already_exists(): void
    {
        $this->expectNotToPerformAssertions();

        $vkId = new NumericVkId(25);
        $fullName = new FullName('John', 'Doe');
        $queryResult = new QueryResult();
        $queryResult->resultRows = [1];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection
            ->method('query')
            ->willReturn(resolve($queryResult));

        $storage = new ObserverStorage($connection);

        $loop = Factory::create();

        try {
            /** @var Observer $result */
            await($storage->create($vkId, $fullName), $loop);
        } catch (ObserverAlreadyExists $exception) {
            return;
        }

        self::fail(
            'Failed to assert that ObserverStorage::create() will throw an exception when observer already exists'
        );
    }

    /**
     * @throws Exception
     */
    public function test_it_passes_insert_statement_to_connection(): void
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
                ['SELECT 1 FROM observers WHERE vk_id = ?', [$vkId->id()]],
                [$insert, [$fullName->firstName(), $fullName->lastName(), $vkId->id(), true]],
            );

        $storage = new ObserverStorage($connection);

        $loop = Factory::create();

        await($storage->create($vkId, $fullName), $loop);
    }

    /**
     * @throws Exception
     */
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
                self::equalTo([$vkId->id()])
            );

        $storage = new ObserverStorage($connection);

        $loop = Factory::create();

        /** @var Observer $result */
        $result = await($storage->findByVkId($vkId), $loop);

        self::assertEquals(
            new Observer(
                new ObserverId((int) $rawObserver['id']),
                $vkId,
                new FullName($rawObserver['first_name'], $rawObserver['last_name']),
                true,
            ),
            $result
        );
    }

    /** @noinspection BadExceptionsProcessingInspection */
    /**
     * @throws Exception
     */
    public function test_it_rejects_when_observer_was_not_found(): void
    {
        $this->expectNotToPerformAssertions();

        $queryResult = new QueryResult();
        $queryResult->resultRows = [];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection
            ->method('query')
            ->willReturn(resolve($queryResult));

        $storage = new ObserverStorage($connection);

        $loop = Factory::create();

        try {
            await($storage->findByVkId(new NumericVkId(5)), $loop);
        } catch (ObserverWasNotFound $exception) {
            return;
        }

        self::fail(
            'Failed to assert that ObserverStorage::findByVkId() will throw an exception when observer was not found'
        );
    }
}
