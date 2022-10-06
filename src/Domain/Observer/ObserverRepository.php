<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Observer;

use BirthdayReminder\Domain\User\UserId;

interface ObserverRepository
{
    /**
     * @return Observer[]
     */
    public function findAll(): array;

    public function findByUserId(UserId $userId): ?Observer;

    public function save(Observer $observer): void;
}
