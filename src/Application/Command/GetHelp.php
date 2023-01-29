<?php

declare(strict_types=1);

namespace BirthdayReminder\Application\Command;

use BirthdayReminder\Application\Message\Message;
use BirthdayReminder\Domain\User\UserId;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

final class GetHelp extends Command
{
    /**
     * @param iterable<Describable> $describables
     */
    public function __construct(private readonly iterable $describables, private readonly TranslatorInterface $translator)
    {
    }

    protected function executedParsed(UserId $observerId, ParseResult $parseResult): string|TranslatableMessage
    {
        $helpMessage = '';

        foreach ($this->describables as $describable) {
            $helpMessage .= sprintf("%s - %s\n\n", $describable->name(), $describable->description()->trans($this->translator));
        }

        if ($helpMessage === '') {
            return Message::unexpectedError();
        }

        return rtrim($helpMessage, "\n");
    }

    protected function pattern(): string
    {
        return '/^help$/';
    }
}
