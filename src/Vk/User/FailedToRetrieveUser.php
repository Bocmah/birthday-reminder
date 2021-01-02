<?php

declare(strict_types=1);

namespace Vkbd\Vk\User;

use RuntimeException;

final class FailedToRetrieveUser extends RuntimeException
{
    public static function because(string $reason): self
    {
        return new self("Failed to retrieve user. Reason: $reason");
    }
}
