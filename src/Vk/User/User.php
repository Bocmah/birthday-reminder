<?php

declare(strict_types=1);

namespace BirthdayReminder\Vk\User;

use BirthdayReminder\Person\FullName;
use BirthdayReminder\Vk\User\Id\NumericVkId;

final class User
{
    public function __construct(
        private NumericVkId $id,
        private FullName $fullName
    ) {
    }

    public function id(): NumericVkId
    {
        return $this->id;
    }

    public function fullName(): FullName
    {
        return $this->fullName;
    }
}
