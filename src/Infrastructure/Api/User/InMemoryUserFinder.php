<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Api\User;

use BirthdayReminder\Domain\User\User;
use BirthdayReminder\Domain\User\UserFinder;
use BirthdayReminder\Domain\User\UserId;

final class InMemoryUserFinder implements UserFinder
{
    /**
     * @var array<string, User>
     */
    private array $users = [];

    public function findById(UserId $id): ?User
    {
        return $this->users[(string) $id] ?? null;
    }

    public function addUser(User $user): void
    {
        $this->users[(string) $user->id] = $user;
    }
}
