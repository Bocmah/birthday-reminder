<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\User;

interface UserFinder
{
    public function findById(UserId $id): ?User;
}
