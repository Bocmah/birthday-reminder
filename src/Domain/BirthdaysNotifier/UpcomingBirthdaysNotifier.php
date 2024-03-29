<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\BirthdaysNotifier;

use BirthdayReminder\Domain\Date\Calendar;
use BirthdayReminder\Domain\Date\DateFormatter;
use BirthdayReminder\Domain\Messenger\Messenger;
use BirthdayReminder\Domain\Observee\Observee;
use BirthdayReminder\Domain\Observee\ObserveeFormatter;
use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\Observer\Specification\HasBirthdaysOnDate;
use DateTimeImmutable;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

final class UpcomingBirthdaysNotifier implements BirthdaysNotifier
{
    public function __construct(
        private readonly Calendar $calendar,
        private readonly TranslatorInterface $translator,
        private readonly DateFormatter $dateFormatter,
        private readonly ObserveeFormatter $observeeFormatter,
        private readonly Messenger $messenger,
    ) {
    }

    public function notify(Observer $observer): void
    {
        Assert::true($this->canNotify($observer));

        $birthdays = array_map(
            fn (DateTimeImmutable $date): string => $this->composeNotification($date, $observer->birthdaysOnDate($date)),
            [$this->calendar->today(), $this->calendar->tomorrow()],
        );

        $this->messenger->sendMessage($observer->id, implode('\n\n', array_filter($birthdays)));
    }

    public function canNotify(Observer $observer): bool
    {
        return (new HasBirthdaysOnDate($this->calendar->today()))
            ->or(new HasBirthdaysOnDate($this->calendar->tomorrow()))
            ->isSatisfiedBy($observer);
    }

    /**
     * @param Observee[] $observees
     */
    private function composeNotification(DateTimeImmutable $date, array $observees): string
    {
        if (\count($observees) > 0) {
            $message = $this->translator->trans('birthdays_on_date', ['%date%' => mb_ucfirst($this->dateFormatter->format($date))]);
            $message .= '\n\n';
        } else {
            $message = '';
        }

        $message .= implode(
            '\n',
            array_map(fn (Observee $observee): string => $this->observeeFormatter->format($observee), $observees)
        );

        return $message;
    }
}
