<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\BirthdaysNotifier;

use BirthdayReminder\Domain\Observer\Observer;
use LogicException;

class BirthdaysNotifierSelector
{
    /**
     * @param iterable<BirthdaysNotifier> $notifiers
     */
    public function __construct(private readonly iterable $notifiers)
    {
    }

    public function selectNotifierForObserver(Observer $observer): BirthdaysNotifier
    {
        foreach ($this->notifiers as $notifier) {
            if ($notifier->canNotify($observer)) {
                return $notifier;
            }
        }

        throw new LogicException('No notifier was found for observer');
    }
}
