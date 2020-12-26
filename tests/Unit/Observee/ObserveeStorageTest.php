<?php

declare(strict_types=1);

namespace Tests\Unit\Observee;

use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use Vkbd\Observee\Observee;
use Vkbd\Observee\ObserveeAlreadyExists;
use Vkbd\Observee\ObserveeStorage;
use Vkbd\Observer\ObserverId;
use Vkbd\Person\FullName;
use Vkbd\Vk\NumericVkId;

use function Clue\React\Block\await;
use function React\Promise\resolve;

final class ObserveeStorageTest extends TestCase
{
    /**
     * @noinspection BadExceptionsProcessingInspection
     *
     * @throws Exception
     */
    public function test_it_checks_if_observee_already_exists(): void
    {
        $this->expectNotToPerformAssertions();

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

        $loop = Factory::create();

        try {
            /** @var Observee $result */
            await($storage->create(
                $observerId,
                $vkId,
                $fullName,
                $birthdate
            ), $loop);
        } catch (ObserveeAlreadyExists $exception) {
            return;
        }

        self::fail(
            'Failed to assert that ObserveeStorage::create() will throw an exception when observee already exists'
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
                    [$observerId->id(), $vkId->id()]
                ],
                [$insert, [$observerId->id(), $fullName->firstName(), $fullName->lastName(), $vkId->id(), '1990-05-10']],
            );

        $storage = new ObserveeStorage($connection);

        $loop = Factory::create();

        await($storage->create(
            $observerId,
            $vkId,
            $fullName,
            $birthdate
        ), $loop);
    }
}
