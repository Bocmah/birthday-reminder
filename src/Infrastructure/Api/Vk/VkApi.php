<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Api\Vk;

use BirthdayReminder\Infrastructure\Http\Middleware\ReceivedInappropriateHttpStatusCode;
use BirthdayReminder\Infrastructure\Util\Json;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use RuntimeException;

class VkApi
{
    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly RequestFactoryInterface $requestFactory,
    ) {
    }

    /**
     * @param array<string, string> $params
     */
    public function callMethod(VkApiMethod $method, array $params): mixed
    {
        $request = $this->requestFactory->createRequest('GET', $this->buildUri($method, $params));

        try {
            $rawResponse = $this->httpClient->sendRequest($request)->getBody()->getContents();
        } catch (ClientExceptionInterface $exception) {
            throw new RuntimeException('Error while processing request to VK API', $exception->getCode(), $exception);
        } catch (ReceivedInappropriateHttpStatusCode $exception) {
            throw new RuntimeException(
                sprintf('Received inappropriate HTTP status code from VK API: %d', $exception->response->getStatusCode()),
                $exception->getCode(),
                $exception,
            );
        }

        if ($rawResponse === '') {
            return null;
        }

        return $this->decodeResponse($rawResponse);
    }

    /**
     * @param array<string, string> $params
     */
    private function buildUri(VkApiMethod $method, array $params): string
    {
        return $method->value . '?' . http_build_query($params);
    }

    private function decodeResponse(string $rawResponse): mixed
    {
        /** @var array{response: mixed} $response */
        $response = Json::decode($rawResponse);

        return $response['response'] ?? null;
    }
}
