<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Api\Vk;

use BirthdayReminder\Infrastructure\Api\Vk\VkApi;
use BirthdayReminder\Infrastructure\Api\Vk\VkApiMethod;
use BirthdayReminder\Infrastructure\Http\Middleware\ReceivedInappropriateHttpStatusCode;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

final class VkApiTest extends TestCase
{
    /**
     * @var MockObject&ClientInterface
     */
    private readonly MockObject $httpClient;

    /**
     * @var MockObject&RequestFactoryInterface
     */
    private readonly MockObject $requestFactory;

    private readonly VkApi $vkApi;

    /**
     * @test
     */
    public function callsMethod(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $stream = $this->createMock(StreamInterface::class);

        $stream
            ->method('getContents')
            ->willReturn('{"response":[{"id":7,"first_name":"John","last_name":"Doe"}]}');

        $response
            ->method('getBody')
            ->willReturn($stream);

        $this->requestFactory
            ->method('createRequest')
            ->with('GET', 'users.get?user_ids=7')
            ->willReturn($request);

        $this->httpClient
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);

        $this->assertEquals(
            [['id' => '7', 'first_name' => 'John', 'last_name' => 'Doe']],
            $this->vkApi->callMethod(VkApiMethod::GetUser, ['user_ids' => '7']),
        );
    }

    /**
     * @test
     */
    public function failsWhenHttpClientThrewException(): void
    {
        $request = $this->createMock(RequestInterface::class);

        $clientException = new class (code: 123) extends Exception implements ClientExceptionInterface {};

        $this->requestFactory
            ->method('createRequest')
            ->willReturn($request);

        $this->httpClient
            ->method('sendRequest')
            ->with($request)
            ->willThrowException($clientException);

        $this->expectExceptionObject(new RuntimeException('Error while processing request to VK API', 123, $clientException));

        $this->vkApi->callMethod(VkApiMethod::GetUser, ['user_ids' => '7']);
    }

    /**
     * @test
     */
    public function failsOnInappropriateHttpStatusCode(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $response
            ->method('getStatusCode')
            ->willReturn(500);

        $exception = new ReceivedInappropriateHttpStatusCode($response);

        $this->requestFactory
            ->method('createRequest')
            ->willReturn($request);

        $this->httpClient
            ->method('sendRequest')
            ->with($request)
            ->willThrowException($exception);

        $this->expectExceptionObject(new RuntimeException('Received inappropriate HTTP status code from VK API: 500', 0, $exception));

        $this->vkApi->callMethod(VkApiMethod::GetUser, ['user_ids' => '7']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->requestFactory = $this->createMock(RequestFactoryInterface::class);

        $this->vkApi = new VkApi($this->httpClient, $this->requestFactory);
    }
}
