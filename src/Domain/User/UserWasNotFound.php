<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\User;

use RuntimeException;

final class UserWasNotFound extends RuntimeException
{
    public static function withId(UserId $id): self
    {
        return new self(sprintf('User with id %s was not found', $id));
    }
}
