<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\BirthdaysNotifier;

use BirthdayReminder\Domain\User\UserId;

interface MessageSender
{
    public function send(UserId $receiver, string $message): void;
}
