<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Date;

use BirthdayReminder\Domain\Date\Calendar;
use BirthdayReminder\Domain\Date\Date;
use BirthdayReminder\Domain\Date\DateFormatter;
use DateTimeImmutable;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RelativeOrFallbackDateFormatter implements DateFormatter
{
    public function __construct(
        private readonly DateFormatter $fallbackFormatter,
        private readonly Calendar $calendar,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function format(DateTimeImmutable $date): string
    {
        return match (true) {
            Date::isSameDay($date, $this->calendar->today()) => $this->translator->trans('date.today'),

            Date::isSameDay($date, $this->calendar->tomorrow()) => $this->translator->trans('date.tomorrow'),

            default => $this->fallbackFormatter->format($date),
        };
    }
}
