<?php

declare(strict_types=1);

namespace Vkbd\Middleware;

use JsonException;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;

final class DecodeJsonRequest
{
    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @psalm-param callable(ServerRequestInterface):(ResponseInterface|PromiseInterface) $next
     *
     * @throws JsonException
     *
     * @return PromiseInterface|ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $contentType = $request->getHeaderLine('Content-type');
        if ($contentType === 'application/json') {
            $body = $request->getBody()->getContents();
            /** @var array $decodedBody */
            $decodedBody = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

            $request = $request->withParsedBody($decodedBody);
        }

        return $next($request);
    }
}
