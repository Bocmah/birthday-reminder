<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use Exception;
use PHPUnit\Framework\TestCase;
use React\Http\Message\ServerRequest;
use Vkbd\Middleware\DecodeJsonRequest;
use Psr\Http\Message\ServerRequestInterface;

use function RingCentral\Psr7\stream_for;

final class DecodeJsonRequestTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_it_decodes_json_request(): void
    {
        $body = ['foo' => 'bar'];
        $jsonBody = json_encode($body, JSON_THROW_ON_ERROR);

        $request = (new ServerRequest('GET', 'https://example.com/', ['Content-type' => 'application/json']))
            ->withBody(stream_for($jsonBody));

        $middleware = new DecodeJsonRequest();
        /** @var ServerRequestInterface $requestWithDecodedBody */
        $requestWithDecodedBody = $middleware(
            $request,
            static fn (ServerRequestInterface $request): ServerRequestInterface => $request
        );

        self::assertEquals($body, $requestWithDecodedBody->getParsedBody());
    }

    /**
     * @throws Exception
     */
    public function test_it_does_not_decode_request_which_does_not_have_json_content_type(): void
    {
        $body = ['foo' => 'bar'];
        $jsonBody = json_encode($body, JSON_THROW_ON_ERROR);

        $request = (new ServerRequest('GET', 'https://example.com/'))
            ->withBody(stream_for($jsonBody));

        $middleware = new DecodeJsonRequest();
        /** @var ServerRequestInterface $requestAfterMiddleware */
        $requestAfterMiddleware = $middleware(
            $request,
            static fn (ServerRequestInterface $request): ServerRequestInterface => $request
        );

        self::assertEquals($jsonBody, $requestAfterMiddleware->getBody());
        self::assertNull($requestAfterMiddleware->getParsedBody());
    }
}
