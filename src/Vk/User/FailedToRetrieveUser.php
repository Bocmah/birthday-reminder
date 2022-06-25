<?php

declare(strict_types=1);

namespace BirthdayReminder\Vk\User;

use RuntimeException;

class FailedToRetrieveUser extends RuntimeException
{
    public static function because(string $reason): self
    {
        return new self("Failed to retrieve user. Reason: $reason");
    }
}
