<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Date;

use BirthdayReminder\Domain\Date\Calendar;
use BirthdayReminder\Domain\Date\Date;
use BirthdayReminder\Domain\Date\DateFormatter;
use DateTimeImmutable;
use IntlDateFormatter as StdlibIntlDateFormatter;

final class IntlDateFormatter implements DateFormatter
{
    public function __construct(
        private readonly string $locale,
        private readonly string $timezone,
        private readonly Calendar $calendar,
    ) {
    }

    public function format(DateTimeImmutable $date): string
    {
        $formatter = match (true) {
            Date::isSameDay($date, $this->calendar->today()),
            Date::isSameDay($date, $this->calendar->tomorrow()) => $this->relativeFormatter(),

            default => $this->normalFormatter(),
        };

        return $formatter->format($date);
    }

    private function relativeFormatter(): StdlibIntlDateFormatter
    {
        return new StdlibIntlDateFormatter(
            $this->locale,
            StdlibIntlDateFormatter::RELATIVE_SHORT,
            StdlibIntlDateFormatter::NONE,
            $this->timezone,
        );
    }

    private function normalFormatter(): StdlibIntlDateFormatter
    {
        return new StdlibIntlDateFormatter(
            $this->locale,
            StdlibIntlDateFormatter::NONE,
            StdlibIntlDateFormatter::NONE,
            $this->timezone,
            null,
            'd MMMM'
        );
    }
}
