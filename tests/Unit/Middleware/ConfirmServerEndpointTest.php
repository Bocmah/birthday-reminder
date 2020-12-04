<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use Exception;
use JsonException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use Vkbd\Middleware\ConfirmServerEndpoint;
use React\Http\Message\ServerRequest;

final class ConfirmServerEndpointTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_it_returns_response_with_confirmation_token_on_confirmation_request(): void
    {
        $confirmationToken = 'test_confirmation_token';
        $confirmationEventName = 'test_confirmation_event_name';

        $request = (new ServerRequest('GET', 'https://example.com/', ['Content-type' => 'application/json']))
            ->withParsedBody(['type' => $confirmationEventName]);

        $middleware = new ConfirmServerEndpoint($confirmationToken, $confirmationEventName);

        $result = $middleware(
            $request,
            static fn (ServerRequestInterface $request): ResponseInterface => new Response(201, [], 'foobar'),
        );

        self::assertInstanceOf(ResponseInterface::class, $result);
        self::assertSame(200, $result->getStatusCode());
        self::assertSame($confirmationToken, $result->getBody()->getContents());
    }

    /**
     * @throws JsonException
     */
    public function test_it_passes_non_confirmation_request_to_next_middleware(): void
    {
        $request = (new ServerRequest('GET', 'https://example.com/', ['Content-type' => 'application/json']))
            ->withParsedBody(['type' => 'message']);

        $middleware = new ConfirmServerEndpoint('test_confirmation_token', 'test_confirmation_event_name');

        $result = $middleware(
            $request,
            static fn (ServerRequestInterface $request): ResponseInterface => new Response(
                201,
                [],
                json_encode(['foo' => 'bar'], JSON_THROW_ON_ERROR),
            )
        );

        self::assertInstanceOf(ResponseInterface::class, $result);
        self::assertSame(201, $result->getStatusCode());
        self::assertSame(
            ['foo' => 'bar'],
            json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR),
        );
    }
}
