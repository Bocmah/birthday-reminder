<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Observer\Exception;

use BirthdayReminder\Domain\User\UserId;
use RuntimeException;

final class ObserverWasNotFoundInTheSystem extends RuntimeException
{
    public static function withUserId(UserId $id): self
    {
        return new self(sprintf('Observer with user id %s was not found in the system', (string) $id));
    }
}
