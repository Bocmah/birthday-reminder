<?php

declare(strict_types=1);

namespace Tests\Constraint\Promise;

use Exception;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\StringContains;
use React\Promise\PromiseInterface;
use Webmozart\Assert\Assert;

use function Tests\await;

final class PromiseRejectsWithExceptionMessage extends Constraint
{
    /**
     * @param string $expectedExceptionMessage
     */
    public function __construct(
        private string $expectedExceptionMessage
    ) {
    }

    protected function matches($other): bool
    {
        Assert::isInstanceOf($other, PromiseInterface::class);

        try {
            await($other);
        } catch (Exception $exception) {
            /** @var bool */
            return (new StringContains($this->expectedExceptionMessage))
                ->evaluate($exception->getMessage(), '', true);
        }

        return false;
    }

    protected function failureDescription($other): string
    {
        return 'promise rejects with expected exception message';
    }

    public function toString(): string
    {
        return "promise rejects with exception message containing \"{$this->expectedExceptionMessage}\"";
    }
}
