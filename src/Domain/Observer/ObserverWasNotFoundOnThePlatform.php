<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Observer;

use BirthdayReminder\Domain\User\UserId;
use RuntimeException;

final class ObserverWasNotFoundOnThePlatform extends RuntimeException
{
    public static function withUserId(UserId $id): self
    {
        return new self(sprintf('Observer with user id %s was not found on the platform', (string) $id));
    }
}
