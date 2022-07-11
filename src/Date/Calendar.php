<?php

declare(strict_types=1);

namespace BirthdayReminder\Date;

use DateTimeImmutable;

interface Calendar
{
    public function today(): DateTimeImmutable;

    public function tomorrow(): DateTimeImmutable;
}
