<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Http\Middleware;

use BirthdayReminder\Infrastructure\Http\Middleware\AddRequiredVkParametersToQuery;
use GuzzleHttp\Promise\PromiseInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * @covers \BirthdayReminder\Infrastructure\Http\Middleware\AddRequiredVkParametersToQuery
 */
final class AddRequiredVkParametersToQueryTest extends TestCase
{
    private const VK_API_VERSION = '5.258';

    private const VK_ACCESS_TOKEN = 'some_access_token';

    private readonly AddRequiredVkParametersToQuery $middleware;

    /**
     * @test
     *
     * @dataProvider addRequiredParametersProvider
     */
    public function addRequiredParameters(string $query, string $expected): void
    {
        $request = $this->createMock(RequestInterface::class);
        $uri = $this->createMock(UriInterface::class);

        $uri->method('getQuery')->willReturn($query);
        $request->method('getUri')->willReturn($uri);

        $uri
            ->method('withQuery')
            ->willReturnCallback(function (string $query): UriInterface {
                $newUri = $this->createMock(UriInterface::class);
                $newUri->method('getQuery')->willReturn($query);

                return $newUri;
            });

        $request
            ->method('withUri')
            ->willReturnCallback(function (UriInterface $uri): RequestInterface {
                $newRequest = $this->createMock(RequestInterface::class);
                $newRequest->method('getUri')->willReturn($uri);

                return $newRequest;
            });

        $handler = ($this->middleware)(self::VK_API_VERSION, self::VK_ACCESS_TOKEN);

        $checker = function (RequestInterface $request) use ($expected): PromiseInterface {
            $this->assertEquals($expected, $request->getUri()->getQuery());

            return $this->createMock(PromiseInterface::class);
        };

        $handler($checker)($request, []);
    }

    /**
     * @see addRequiredParameters()
     *
     * @return iterable<string, array{query: string, expected: string}>
     */
    public function addRequiredParametersProvider(): iterable
    {
        yield 'query string with existing parameters' => [
            'query'    => 'foo=bar&baz=qux',
            'expected' => sprintf('foo=bar&baz=qux&v=%s&access_token=%s', self::VK_API_VERSION, self::VK_ACCESS_TOKEN),
        ];

        yield 'empty query string' => [
            'query'    => '',
            'expected' => sprintf('v=%s&access_token=%s', self::VK_API_VERSION, self::VK_ACCESS_TOKEN),
        ];

        yield 'params with same name get overridden' => [
            'query'    => 'v=6&access_token=another_access_token',
            'expected' => sprintf('v=%s&access_token=%s', self::VK_API_VERSION, self::VK_ACCESS_TOKEN),
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->middleware = new AddRequiredVkParametersToQuery();
    }
}
