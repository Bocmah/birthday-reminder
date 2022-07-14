<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Messenger;

use BirthdayReminder\Domain\User\UserId;

interface Messenger
{
    public function sendMessage(UserId $to, string $text): void;
}
