<?php

declare(strict_types=1);

namespace BirthdayReminder\Application\Command;

use LogicException;

final class CommandSelector
{
    /**
     * @param iterable<Command> $commands
     */
    public function __construct(private readonly iterable $commands)
    {
    }

    public function select(string $text): Command
    {
        foreach ($this->commands as $command) {
            if ($command->matches($text)) {
                return $command;
            }
        }

        throw new LogicException(sprintf('No command was found for "%s"', $text));
    }
}
