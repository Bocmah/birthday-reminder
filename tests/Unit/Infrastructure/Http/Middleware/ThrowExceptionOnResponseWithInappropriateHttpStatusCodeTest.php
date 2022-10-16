<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Http\Middleware;

use BirthdayReminder\Infrastructure\Http\Middleware\ReceivedInappropriateHttpStatusCode;
use BirthdayReminder\Infrastructure\Http\Middleware\ThrowExceptionOnResponseWithInappropriateHttpStatusCode;
use Http\Promise\Promise;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \BirthdayReminder\Infrastructure\Http\Middleware\ThrowExceptionOnResponseWithInappropriateHttpStatusCode
 */
final class ThrowExceptionOnResponseWithInappropriateHttpStatusCodeTest extends TestCase
{
    /**
     * @test
     */
    public function throwExceptionOnInappropriateStatusCode(): void
    {
        $middleware = (new ThrowExceptionOnResponseWithInappropriateHttpStatusCode([200]))();

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(500);

        $next = function () use ($response): Promise {
            $promise = $this->createMock(Promise::class);
            $promise->method('then')->willReturnCallback(function (callable $onFulfilled) use ($response) {
                $onFulfilled($response);

                return $this->createMock(Promise::class);
            });

            return $promise;
        };

        $this->expectExceptionObject(ReceivedInappropriateHttpStatusCode::fromResponse($response));

        $middleware($next)($this->createMock(RequestInterface::class), []);
    }

    /**
     * @test
     */
    public function doNothingOnAppropriateStatusCode(): void
    {
        $middleware = (new ThrowExceptionOnResponseWithInappropriateHttpStatusCode([200]))();

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);

        $next = function () use ($response): Promise {
            $promise = $this->createMock(Promise::class);
            $promise->method('then')->willReturnCallback(function (callable $onFulfilled) use ($response) {
                $onFulfilled($response);

                return $this->createMock(Promise::class);
            });

            return $promise;
        };

        $this->expectNotToPerformAssertions();

        $middleware($next)($this->createMock(RequestInterface::class), []);
    }

    /**
     * @test
     */
    public function atLeastOneAllowedStatusCodeMustBePresent(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('At least 1 allowed status code must be provided');

        new ThrowExceptionOnResponseWithInappropriateHttpStatusCode([]);
    }
}
