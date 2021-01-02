<?php

declare(strict_types=1);

namespace Vkbd\Vk\Api;

use RuntimeException;

final class FailedToCallVkApiMethod extends RuntimeException
{
    public static function unexpectedStatusCode(int $code): self
    {
        return new self("Failed to call VK api. Received unexpected status code $code");
    }

    public static function withMethodAndReason(string $method, string $reason): self
    {
        return new self("Failed to call method $method. Reason: $reason");
    }
}
