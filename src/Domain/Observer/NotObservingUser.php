<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Observer;

use BirthdayReminder\Domain\User\UserId;
use RuntimeException;

final class NotObservingUser extends RuntimeException
{
    public static function withId(UserId $id): self
    {
        return new self(sprintf('Not observing user with id %s', $id));
    }
}
