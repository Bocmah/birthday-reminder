<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Observer\Exception;

use BirthdayReminder\Domain\User\UserId;
use RuntimeException;

final class AlreadyObservingUser extends RuntimeException
{
    public static function withId(UserId $id): self
    {
        return new self(sprintf('Already observing user with id %s', (string) $id));
    }
}
