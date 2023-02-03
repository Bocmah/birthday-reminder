<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Date;

use BirthdayReminder\Domain\Date\Calendar;
use DateTimeImmutable;

final class FixedCalendar implements Calendar
{
    public function __construct(private readonly DateTimeImmutable $today, private readonly DateTimeImmutable $tomorrow)
    {
    }

    public function today(): DateTimeImmutable
    {
        return $this->today;
    }

    public function tomorrow(): DateTimeImmutable
    {
        return $this->tomorrow;
    }
}
