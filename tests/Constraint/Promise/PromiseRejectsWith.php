<?php

declare(strict_types=1);

namespace Tests\Constraint\Promise;

use Exception;
use PHPUnit\Framework\Constraint\Constraint;
use React\EventLoop\Factory;
use React\Promise\PromiseInterface;
use React\Promise\Timer\TimeoutException;
use RuntimeException;
use Tests\Exception\ActualValueIsNotAPromiseException;
use Tests\Exception\LoopTimedOutException;

use function Clue\React\Block\await;

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
            await($other, Factory::create());
        } catch (TimeoutException $exception) {
            throw new RuntimeException('Loop timed out');
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
        return "rejects with {$this->expectedException}";
    }
}
