<?php

declare(strict_types=1);

namespace Vkbd\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use Vkbd\Vk\Event;

final class RejectUnsupportedEvents
{
    /** @var Event[] */
    private array $supportedEvents;

    /**
     * @param Event[] $supportedEvents
     */
    public function __construct(array $supportedEvents)
    {
        $this->supportedEvents = $supportedEvents;
    }

    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @psalm-param callable(ServerRequestInterface):(ResponseInterface|PromiseInterface) $next
     *
     * @return ResponseInterface|PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        /** @var array $body */
        $body = $request->getParsedBody();

        if (!isset($body['type'])) {
            return new Response(400, [], "Body of the request must have a 'type' key");
        }

        if ($this->eventIsNotSupported((string) $body['type'])) {
            return new Response(400, [], 'Unsupported event');
        }

        return $next($request);
    }

    private function eventIsNotSupported(string $event): bool
    {
        $filtered = array_filter(
            $this->supportedEvents,
            static fn (Event $supportedEvent) => $supportedEvent->is($event)
        );

        return \count($filtered) === 0;
    }
}
