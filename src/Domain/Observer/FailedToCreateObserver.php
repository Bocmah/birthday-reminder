<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Observer;

use RuntimeException;

final class FailedToCreateObserver extends RuntimeException
{
    public static function because(string $reason): self
    {
        return new self("Failed to create observer. Reason: $reason");
    }
}
