<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Date;

use DateTimeImmutable;

interface DateFormatter
{
    public function format(DateTimeImmutable $date): string;
}
