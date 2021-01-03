<?php

declare(strict_types=1);

namespace Tests;

use React\EventLoop\Factory;
use React\Promise\PromiseInterface;

use function Clue\React\Block\await;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public function awaitPromise(PromiseInterface $promise): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        await($promise, Factory::create());
    }
}
