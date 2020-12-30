<?php

declare(strict_types=1);

namespace Vkbd\Vk\Api;

use Exception;
use React\Http\Browser;
use React\Promise\PromiseInterface;

final class VkApi
{
    private Config $config;
    private Browser $browser;

    public function __construct(Config $config, Browser $browser)
    {
        $this->config = $config;
        $this->browser = $browser;
    }

    /**
     * @param string $method
     * @param array<string, mixed> $parameters
     *
     * @return PromiseInterface
     */
    public function callMethod(string $method, array $parameters): PromiseInterface
    {
        return $this->browser
            ->get($this->buildUrl($method, $parameters))
            ->otherwise(static function (Exception $exception) use ($method) {
                throw FailedToCallVkApiMethod::withMethodAndReason($method, $exception->getMessage());
            });
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
