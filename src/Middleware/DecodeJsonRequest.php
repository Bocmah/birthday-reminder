<?php

declare(strict_types=1);

namespace Vkbd\Middleware;

use JsonException;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

final class DecodeJsonRequest
{
    /**
     * @param ServerRequestInterface $request
     * @param callable               $next
     *
     * @throws JsonException
     *
     * @return PromiseInterface|ServerRequestInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $contentType = $request->getHeaderLine('Content-type');
        if ($contentType === 'application/json') {
            $body = $request->getBody()->getContents();
            $decodedBody = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

            $request = $request->withParsedBody($decodedBody);
        }

        return $next($request);
    }
}
