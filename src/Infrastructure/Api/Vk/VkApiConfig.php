<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Api\Vk;

final class VkApiConfig
{
    public function __construct(
        public readonly string $baseUri,
        public readonly string $version,
        public readonly string $accessToken,
    ) {
    }
}
