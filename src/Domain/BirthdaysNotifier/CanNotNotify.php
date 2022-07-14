<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\BirthdaysNotifier;

use RuntimeException;

final class CanNotNotify extends RuntimeException
{
    public static function becauseObserverDoesNotHaveUpcomingBirthdays(): self
    {
        return new self('Can not notify observer because he does not have upcoming birthdays');
    }
}
