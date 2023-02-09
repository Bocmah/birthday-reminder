<?php

declare(strict_types=1);

namespace BirthdayReminder\Application;

use BirthdayReminder\Domain\BirthdaysNotifier\BirthdaysNotifierSelector;
use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\Observer\ObserverRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
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
        $observers = $this->observerRepository->findAll();

        if ($observers === []) {
            $this->logger->info('There are no observers in the system. Nobody to notify.');

            return;
        }

        $stopwatch = new Stopwatch();
        $stopwatch->start('observers_notification');

        foreach ($observers as $observer) {
            $this->notify($observer);
        }

        $event = $stopwatch->stop('observers_notification');

        $this->logger->info(sprintf('Finished observers notification. Took %s ms. Used %.2F MiB of memory.', $event->getDuration(), $event->getMemory() / 1024 / 1024));
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
