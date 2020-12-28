<?php

declare(strict_types=1);

namespace Tests\Unit\Vk\Api;

use PHPUnit\Framework\TestCase;
use Vkbd\Vk\Api\Config;

final class ConfigTest extends TestCase
{
    public function test_it_can_not_be_created_with_invalid_url(): void
    {
        $this->expectExceptionMessage('Base URL must be a valid URL');

        new Config('gewnfkelw', '1321g3e3', '5.10');
    }

    public function test_it_can_not_be_created_with_empty_token(): void
    {
        $this->expectExceptionMessage('VK API token must not be empty');

        new Config('https://api.vk.com/', '', '5.10');
    }

    public function test_it_can_not_be_created_with_empty_version(): void
    {
        $this->expectExceptionMessage('VK version must not be empty');

        new Config('https://api.vk.com/', '1321g3e3', '');
    }
}
