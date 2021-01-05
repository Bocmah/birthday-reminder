<?php

declare(strict_types=1);

namespace Tests\Constraint\Promise;

use Exception;
use PHPUnit\Framework\Constraint\Constraint;
use React\Promise\PromiseInterface;
use RuntimeException;

use function Tests\await;

final class PromiseRejectsWith extends Constraint
{
    /** @var class-string */
    private string $expectedException;

    /**
     * @param class-string $expectedException
     */
    public function __construct(string $expectedException)
    {
        $this->expectedException = $expectedException;
    }

    protected function matches($other): bool
    {
        if (!($other instanceof PromiseInterface)) {
            throw new RuntimeException('Actual value is not a Promise');
        }

        try {
            await($other);
        } catch (Exception $exception) {
            if ($exception instanceof $this->expectedException) {
                return true;
            }
        }

        return false;
    }

    protected function failureDescription($other): string
    {
        return 'promise rejects with expected exception';
    }

    public function toString(): string
    {
        return "promise rejects with {$this->expectedException}";
    }
}
