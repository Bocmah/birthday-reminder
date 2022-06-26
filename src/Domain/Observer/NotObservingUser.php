<?php

declare(strict_types=1);

namespace BirthdayReminder\Observer\Domain;

use BirthdayReminder\Platform\PlatformUserId;

final class NotObservingUser extends \RuntimeException
{
    public static function withId(PlatformUserId $id): self
    {
        return new self(sprintf('Not observing user with id %s', $id));
    }
}
