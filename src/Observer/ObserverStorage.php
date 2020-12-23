<?php

declare(strict_types=1);

namespace Vkbd\Observer;

use React\MySQL\ConnectionInterface;
use React\Promise\PromiseInterface;
use Vkbd\Vk\NumericVkId;
use Vkbd\Person\FullName;
use React\MySQL\QueryResult;

use function React\Promise\reject;
use function React\Promise\resolve;

final class ObserverStorage
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function create(NumericVkId $vkId, FullName $fullName, bool $shouldAlwaysBeNotified = true): PromiseInterface
    {
        return $this
            ->observerDoesNotExist($vkId)
            ->then(fn () => $this->connection->query(
                'INSERT INTO observers (first_name, last_name, vk_id, should_always_be_notified) VALUES (?, ?, ?, ?)',
                [$fullName->firstName(), $fullName->lastName(), $vkId->id(), $shouldAlwaysBeNotified],
            ));
    }

    private function observerDoesNotExist(NumericVkId $vkId): PromiseInterface
    {
        return $this->connection
            ->query(
                'SELECT 1 FROM observers WHERE vk_id = ?',
                [$vkId->id()]
            )
            ->then(
                fn (QueryResult $result): PromiseInterface => empty($result->resultRows) ?
                    resolve() :
                    reject(new ObserverAlreadyExists())
            );
    }
}
