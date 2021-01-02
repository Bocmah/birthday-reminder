<?php

declare(strict_types=1);

namespace Vkbd\Vk\Api;

use Exception;
use Psr\Http\Message\ResponseInterface;
use React\Http\Browser;
use React\Promise\PromiseInterface;

final class VkApi implements VkApiInterface
{
    private Config $config;
    private Browser $browser;

    public function __construct(Config $config, Browser $browser)
    {
        $this->config = $config;
        $this->browser = $browser;
    }

    /**
     * @inheritDoc
     */
    public function callMethod(string $method, array $parameters): PromiseInterface
    {
        return $this->browser
            ->get($this->buildUrl($method, $parameters))
            ->then(
                static function (ResponseInterface $response): array {
                    if ($response->getStatusCode() !== 200) {
                        throw FailedToCallVkApiMethod::unexpectedStatusCode($response->getStatusCode());
                    }

                    return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
                },
                static function (Exception $exception) use ($method): void {
                    throw FailedToCallVkApiMethod::withMethodAndReason($method, $exception->getMessage());
                }
            );
    }

    /**
     * @param string $method
     * @param array<string, mixed> $parameters
     *
     * @return string
     */
    private function buildUrl(string $method, array $parameters): string
    {
        return $this->config->baseUrl() . "/method/$method?" . http_build_query(
            [
                'access_token' => $this->config->token(),
                'v' => $this->config->version(),
            ] + $parameters
        );
    }
}
