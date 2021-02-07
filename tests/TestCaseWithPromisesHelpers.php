<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use React\Promise\PromiseInterface;
use Tests\Constraint\Promise\PromiseRejectsWith;
use Tests\Constraint\Promise\PromiseRejectsWithExceptionMessage;
use Tests\Constraint\Promise\PromiseResolvesWith;

abstract class TestCaseWithPromisesHelpers extends TestCase
{
    public function assertResolvesWith(PromiseInterface $promise, mixed $value, string $message = ''): void
    {
        self::assertThat($promise, new PromiseResolvesWith($value), $message);
    }

    /**
     * @param PromiseInterface $promise
     * @param class-string $exception
     * @param string $message
     */
    public function assertRejectsWith(PromiseInterface $promise, string $exception, string $message = ''): void
    {
        self::assertThat($promise, new PromiseRejectsWith($exception), $message);
    }

    public function assertRejectsWithExceptionMessage(PromiseInterface $promise, string $exceptionMessage, string $message = ''): void
    {
        self::assertThat(
            $promise,
            new PromiseRejectsWithExceptionMessage($exceptionMessage),
            $message
        );
    }
}
