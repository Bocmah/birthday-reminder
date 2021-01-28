<?php

declare(strict_types=1);

namespace Vkbd\Vk\Api;

use React\Promise\PromiseInterface;

interface VkApiInterface
{
    /**
     * @param string $method
     * @param array<string, mixed> $parameters
     *
     * @return PromiseInterface<array<string, mixed>>
     */
    public function callMethod(string $method, array $parameters): PromiseInterface;
}
