<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Observee;

use BirthdayReminder\Domain\User\UserId;
use RuntimeException;

final class ObserveeWasNotFoundOnThePlatform extends RuntimeException
{
    public static function withUserId(UserId $id): self
    {
        return new self(sprintf('Observee with user id %s was not found on the platform', $id));
    }
}