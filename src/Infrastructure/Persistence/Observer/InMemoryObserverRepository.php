<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Persistence\Observer;

use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\Observer\ObserverRepository;
use BirthdayReminder\Domain\User\UserId;

final class InMemoryObserverRepository implements ObserverRepository
{
    /**
     * @var array<string, Observer>
     */
    private array $observers = [];

    public function findByUserId(UserId $userId): ?Observer
    {
        return $this->observers[(string) $userId] ?? null;
    }

    public function save(Observer $observer): void
    {
        $this->observers[(string) $observer->id] = $observer;
    }

    public function findAll(): array
    {
        return array_values($this->observers);
    }
}
