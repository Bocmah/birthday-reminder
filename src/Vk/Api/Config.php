<?php

declare(strict_types=1);

namespace Vkbd\Vk\Api;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

final class Config
{
    private string $baseUrl;
    private string $token;
    private string $version;

    public function __construct(string $baseUrl, string $token, string $version)
    {
        if (!filter_var($baseUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Base URL must be a valid URL');
        }

        Assert::stringNotEmpty($token, 'VK API token must not be empty');
        Assert::stringNotEmpty($version, 'VK version must not be empty');

        $this->baseUrl = $baseUrl;
        $this->token = $token;
        $this->version = $version;
    }

    public function baseUrl(): string
    {
        return $this->baseUrl;
    }

    public function token(): string
    {
        return $this->token;
    }

    public function version(): string
    {
        return $this->version;
    }
}
