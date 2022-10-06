<?php

declare(strict_types=1);

namespace BirthdayReminder\Application;

use BirthdayReminder\Domain\BirthdaysNotifier\BirthdaysNotifierSelector;
use BirthdayReminder\Domain\Observer\ObserverRepository;

final class BirthdaysNotifierService
{
    public function __construct(
        private readonly ObserverRepository        $observerRepository,
        private readonly BirthdaysNotifierSelector $notifierSelector,
    ) {
    }

    public function notifyObservers(): void
    {
        foreach ($this->observerRepository->findAll() as $observer) {
            $notifier = $this->notifierSelector->selectNotifierForObserver($observer);

            $notifier->notify($observer);
        }
    }
}
