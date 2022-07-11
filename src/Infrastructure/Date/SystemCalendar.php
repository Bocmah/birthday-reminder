<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Date;

use BirthdayReminder\Date\Calendar;
use DateTimeImmutable;

final class SystemCalendar implements Calendar
{
    public function today(): DateTimeImmutable
    {
        return new DateTimeImmutable('today');
    }

    public function tomorrow(): DateTimeImmutable
    {
        return new DateTimeImmutable('tomorrow');
    }
}
