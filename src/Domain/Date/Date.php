<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Date;

use DateTimeImmutable;

final class Date
{
    public static function isSameDay(DateTimeImmutable $dateA, DateTimeImmutable $dateB): bool
    {
        return $dateA->format('d-m') === $dateB->format('d-m');
    }
}
