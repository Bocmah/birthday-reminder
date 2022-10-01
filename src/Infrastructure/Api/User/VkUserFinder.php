<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Api\User;

use BirthdayReminder\Domain\User\User;
use BirthdayReminder\Domain\User\UserFinder;
use BirthdayReminder\Domain\User\UserId;

final class VkUserFinder implements UserFinder
{
    public function findById(UserId $id): ?User
    {

    }
}
