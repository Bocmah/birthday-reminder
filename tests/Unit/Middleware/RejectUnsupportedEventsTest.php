<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use JsonException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;
use Vkbd\Middleware\RejectUnsupportedEvents;
use Vkbd\Vk\Event;

final class RejectUnsupportedEventsTest extends TestCase
{
    public function test_it_rejects_unsupported_event(): void
    {
        $request = (new ServerRequest('GET', 'https://example.com/'))
            ->withParsedBody(['type' => 'unsupported_event']);

        $middleware = new RejectUnsupportedEvents([new Event(Event::CONFIRMATION), new Event(Event::NEW_MESSAGE)]);

        $result = $middleware(
            $request,
            static fn (ServerRequestInterface $request): ResponseInterface => new Response(200, [], 'foobar')
        );

        self::assertInstanceOf(ResponseInterface::class, $result);
        self::assertSame(400, $result->getStatusCode());
        self::assertSame('Unsupported event', $result->getBody()->getContents());
    }

    /**
     * @throws JsonException
     */
    public function test_it_does_not_reject_supported_event(): void
    {
        $supportedEvents = [new Event(Event::CONFIRMATION), new Event(Event::NEW_MESSAGE)];

        $request = (new ServerRequest('GET', 'https://example.com/'))
            ->withParsedBody(['type' => (string) $supportedEvents[0]]);

        $middleware = new RejectUnsupportedEvents($supportedEvents);

        $result = $middleware(
            $request,
            static fn (ServerRequestInterface $request): ResponseInterface => new Response(
                200,
                [],
                json_encode(['foo' => 'bar'], JSON_THROW_ON_ERROR),
            )
        );

        self::assertInstanceOf(ResponseInterface::class, $result);
        self::assertSame(200, $result->getStatusCode());
        self::assertSame(
            ['foo' => 'bar'],
            json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR),
        );
    }

    public function test_it_rejects_requests_which_do_not_have_type_key_in_the_body(): void
    {
        $request = (new ServerRequest('GET', 'https://example.com/'))
            ->withParsedBody(['foo' => 'bar']);

        $middleware = new RejectUnsupportedEvents([new Event(Event::CONFIRMATION), new Event(Event::NEW_MESSAGE)]);

        $result = $middleware(
            $request,
            static fn (ServerRequestInterface $request): ResponseInterface => new Response(200, [], 'foobar')
        );

        self::assertInstanceOf(ResponseInterface::class, $result);
        self::assertSame(400, $result->getStatusCode());
        self::assertSame("Body of the request must have a 'type' key", $result->getBody()->getContents());
    }
}
