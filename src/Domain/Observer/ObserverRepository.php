<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Observer;

use BirthdayReminder\Domain\User\UserId;

interface ObserverRepository
{
    public function findByUserId(UserId $userId): ?Observer;

    public function save(Observer $observer): void;
}
