<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Date;

use DateTimeImmutable;

final class Date
{
    public static function today(): DateTimeImmutable
    {
        return new DateTimeImmutable('today');
    }

    public static function tomorrow(): DateTimeImmutable
    {
        return new DateTimeImmutable('tomorrow');
    }

    public static function isSameDay(DateTimeImmutable $dateA, DateTimeImmutable $dateB): bool
    {
        return $dateA->format('d-m') === $dateB->format('d-m');
    }

    public static function asDayOfMonth(DateTimeImmutable $date): string
    {
        return $date->format('d-m');
    }
}
