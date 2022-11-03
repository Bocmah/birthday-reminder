<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Date;

use BirthdayReminder\Domain\Date\DateFormatter;
use DateTimeImmutable;

final class FixedFormatDateFormatter implements DateFormatter
{
    public function __construct(private readonly string $format)
    {
    }

    public function format(DateTimeImmutable $date): string
    {
        return $date->format($this->format);
    }
}
