<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\BirthdaysNotifier;

use BirthdayReminder\Domain\Observer\Observer;

final class NullBirthdaysNotifier implements BirthdaysNotifier
{
    public function notify(Observer $observer): void
    {
    }

    public function canNotify(Observer $observer): bool
    {
        return true;
    }
}
