<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\BirthdaysNotifier;

use BirthdayReminder\Domain\Date\Calendar;
use BirthdayReminder\Domain\Messenger\Messenger;
use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\Observer\Specification\DoesntHaveBirthdaysOnDate;
use BirthdayReminder\Domain\Observer\Specification\HasObservees;
use BirthdayReminder\Domain\Observer\Specification\ShouldAlwaysBeNotified;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

final class NoUpcomingBirthdaysNotifier implements BirthdaysNotifier
{
    public function __construct(
        private readonly Calendar $calendar,
        private readonly TranslatorInterface $translator,
        private readonly Messenger $messenger,
    ) {
    }

    public function notify(Observer $observer): void
    {
        Assert::true($this->canNotify($observer));

        $this->messenger->sendMessage($observer->id, $this->translator->trans('no_upcoming_birthdays'));
    }

    public function canNotify(Observer $observer): bool
    {
        return (new HasObservees())
            ->and(new DoesntHaveBirthdaysOnDate($this->calendar->today()))
            ->and(new DoesntHaveBirthdaysOnDate($this->calendar->tomorrow()))
            ->and(new ShouldAlwaysBeNotified())
            ->isSatisfiedBy($observer);
    }
}
