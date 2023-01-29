<?php

declare(strict_types=1);

namespace BirthdayReminder\Application\Command;

use BirthdayReminder\Application\Message\Message;
use BirthdayReminder\Domain\User\UserId;
use Symfony\Component\Translation\TranslatableMessage;

final class UnknownCommand extends Command
{
    protected function executedParsed(UserId $observerId, ParseResult $parseResult): string|TranslatableMessage
    {
        return Message::unknownCommand();
    }

    protected function pattern(): string
    {
        return '/.*/';
    }
}
