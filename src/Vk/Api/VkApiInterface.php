<?php

namespace Vkbd\Vk\Api;

use React\Promise\PromiseInterface;

interface VkApiInterface
{
    /**
     * @param string $method
     * @param array<string, mixed> $parameters
     *
     * @return PromiseInterface
     */
    public function callMethod(string $method, array $parameters): PromiseInterface;
}
