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
use Vkbd\Observer\ObserverStorage;
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
}
