<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Http\Middleware;

use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Webmozart\Assert\Assert;

final class ThrowExceptionOnResponseWithInappropriateHttpStatusCode
{
    /**
     * @param int[] $allowedStatusCodes
     */
    public function __construct(private readonly array $allowedStatusCodes)
    {
        Assert::minCount($this->allowedStatusCodes, 1, 'At least 1 allowed status code must be provided');
    }

    /**
     * @return callable(callable(RequestInterface,array):Promise):callable(RequestInterface,array):Promise
     */
    public function __invoke(): callable
    {
        return fn (callable $next): callable => (
            function (RequestInterface $request, array $options) use ($next): Promise {
                return $next($request, $options)->then(function (ResponseInterface $response): ResponseInterface {
                    $this->ensureAppropriateStatusCode($response);

                    return $response;
                });
            }
        );
    }

    private function ensureAppropriateStatusCode(ResponseInterface $response): void
    {
        if (!in_array($response->getStatusCode(), $this->allowedStatusCodes, true)) {
            throw ReceivedInappropriateHttpStatusCode::fromResponse($response);
        }
    }
}
