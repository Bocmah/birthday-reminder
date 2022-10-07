<?php

declare(strict_types=1);

namespace BirthdayReminder\Application\Command;

use BirthdayReminder\Domain\User\UserId;
use Symfony\Component\Translation\TranslatableMessage;
use Throwable;

abstract class Command
{
    public function matches(string $command): bool
    {
        return (bool) preg_match($this->pattern(), $command);
    }

    public function execute(UserId $observerId, string $command): TranslatableMessage
    {
        try {
            return $this->executedParsed($observerId, $this->parse($command));
        } catch (Throwable $e) {
            throw new ErrorDuringCommandExecution(previous: $e);
        }
    }

    protected function parse(string $command): ParseResult
    {
        preg_match($this->pattern(), $command, $matches);

        return new ParseResult($matches);
    }

    abstract protected function executedParsed(UserId $observerId, ParseResult $parseResult): TranslatableMessage;

    abstract protected function pattern(): string;
}
