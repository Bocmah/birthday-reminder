<?php

declare(strict_types=1);

namespace Vkbd\Vk\Message;

use RuntimeException;

final class FailedToSendMessage extends RuntimeException
{
    public static function because(string $reason): self
    {
        return new self("Failed to send message. Reason: $reason");
    }
}
