<?php

declare(strict_types=1);

namespace Tests\Unit\Observer;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use Vkbd\Observer\Observer;
use Vkbd\Observer\ObserverId;
use Vkbd\Observer\ObserverStorage;
use Vkbd\Person\FullName;
use Vkbd\Vk\NumericVkId;

use function Clue\React\Block\await;
use function React\Promise\resolve;

final class ObserverStorageTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_it_passes_insert_statement_to_connection_and_returns_created_observer(): void
    {
        $vkId = new NumericVkId(25);
        $fullName = new FullName('John', 'Doe');
        $observerId = 1;
        $queryResult = new QueryResult();
        $queryResult->insertId = $observerId;

        /** @var ConnectionInterface|MockObject $connection */
        $connection = $this->createMock(ConnectionInterface::class);

        $connection
            ->method('query')
            ->willReturn(resolve($queryResult));

        $connection
            ->expects(self::once())
            ->method('query')
            ->with(
                'INSERT INTO observers (first_name, last_name, vk_id, should_always_be_notified) VALUES (?, ?, ?, ?)',
                [$fullName->firstName(), $fullName->lastName(), $vkId->id(), true],
            );

        $storage = new ObserverStorage($connection);

        $loop = Factory::create();

        $result = await($storage->create($vkId, $fullName), $loop);

        self::assertEquals($result, new Observer(
            new ObserverId($observerId),
            $vkId,
            $fullName
        ));
    }
}
