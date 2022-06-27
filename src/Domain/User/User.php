<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\User;

use BirthdayReminder\Domain\FullName;

final class User
{
    public function __construct(public readonly UserId $id, public readonly FullName $fullName)
    {
    }
}
