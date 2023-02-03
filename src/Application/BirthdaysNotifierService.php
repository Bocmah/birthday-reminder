<?php

declare(strict_types=1);

namespace BirthdayReminder\Application;

use BirthdayReminder\Domain\BirthdaysNotifier\BirthdaysNotifierSelector;
use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\Observer\ObserverRepository;
use Psr\Log\LoggerInterface;
use Throwable;

final class BirthdaysNotifierService
{
    public function __construct(
        private readonly ObserverRepository $observerRepository,
        private readonly BirthdaysNotifierSelector $notifierSelector,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function notifyObservers(): void
    {
        foreach ($this->observerRepository->findAll() as $observer) {
            $this->notify($observer);
        }
    }

    private function notify(Observer $observer): void
    {
        try {
            $notifier = $this->notifierSelector->selectNotifierForObserver($observer);

            $notifier->notify($observer);
        } catch (Throwable $e) {
            $this->logger->error(
                'Failed to notify observer',
                [
                    'error'    => $e->getMessage(),
                    'observer' => (string) $observer->id,
                ],
            );
        }
    }
}
