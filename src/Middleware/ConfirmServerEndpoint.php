<?php

declare(strict_types=1);

namespace Vkbd\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

final class ConfirmServerEndpoint
{
    public function __construct(
        private string $confirmationToken,
        private string $confirmationEventName
    ) {
    }

    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @psalm-param callable(ServerRequestInterface):(ResponseInterface|PromiseInterface) $next
     *
     * @return ResponseInterface|PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next): PromiseInterface|ResponseInterface
    {
        if ($this->isConfirmationRequest($request)) {
            return new Response(200, [], $this->confirmationToken);
        }

        return $next($request);
    }

    private function isConfirmationRequest(ServerRequestInterface $request): bool
    {
        $body = $request->getParsedBody();

        if (isset($body['type'])) {
            return $body['type'] === $this->confirmationEventName;
        }

        return false;
    }
}
