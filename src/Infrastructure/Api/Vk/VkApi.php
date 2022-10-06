<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Api\Vk;

final class VkApi
{
    public function __construct(private readonly VkApiConfig $config) {
    }

    public function callMethod(string $method, array $params): void
    {

    }
}
