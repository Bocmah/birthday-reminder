<?php

declare(strict_types=1);

namespace Tests\Constraint\Promise;

use Exception;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsEqual;
use React\Promise\PromiseInterface;
use Webmozart\Assert\Assert;

use function Tests\await;

final class PromiseResolvesWith extends Constraint
{
    /** @var mixed */
    private $expectedValue;

    /**
     * @param mixed $expectedValue
     */
    public function __construct($expectedValue)
    {
        $this->expectedValue = $expectedValue;
    }

    /** @noinspection BadExceptionsProcessingInspection */
    protected function matches($other): bool
    {
        Assert::isInstanceOf($other, PromiseInterface::class);

        try {
            /** @psalm-suppress MixedAssignment */
            $result = await($other);
        } catch (Exception $exception) {
            return false;
        }

        /** @var bool */
        return (new IsEqual($this->expectedValue))->evaluate($result, '', true);
    }

    protected function failureDescription($other): string
    {
        return 'promise resolves with value';
    }

    public function toString(): string
    {
        return "resolves with {$this->expectedValue}";
    }
}
