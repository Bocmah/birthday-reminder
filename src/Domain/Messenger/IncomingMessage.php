<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Messenger;

use BirthdayReminder\Domain\User\UserId;

final class IncomingMessage
{
    public function __construct(public readonly UserId $from, public readonly string $text)
    {
    }
}
