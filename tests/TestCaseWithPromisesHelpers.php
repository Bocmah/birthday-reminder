<?php

declare(strict_types=1);

namespace Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use React\Promise\PromiseInterface;
use Tests\Constraint\Promise\PromiseRejectsWith;

use function Clue\React\Block\await;

abstract class TestCaseWithPromisesHelpers extends TestCase
{
    /**
     * @param PromiseInterface $promise
     * @param class-string $exception
     * @param string $message
     */
    public function assertRejectsWith(PromiseInterface $promise, string $exception, string $message = ''): void
    {
        self::assertThat($promise, new PromiseRejectsWith($exception), $message);
    }

    /**
     * @param PromiseInterface $promise
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function await(PromiseInterface $promise)
    {
        return await($promise, Factory::create());
    }
}
