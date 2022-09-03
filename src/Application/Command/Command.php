<?php

declare(strict_types=1);

namespace BirthdayReminder\Application\Command;

use BirthdayReminder\Domain\User\UserId;

abstract class Command
{
    public function matches(string $command): bool
    {
        return (bool) preg_match($this->pattern(), $command);
    }

    /**
     * @return array<int|string, string>
     */
    protected function parse(string $command): array
    {
        preg_match($this->pattern(), $command, $matches);

        return $matches;
    }

    abstract public function execute(UserId $observerId, string $command): void;

    abstract protected function pattern(): string;
}
