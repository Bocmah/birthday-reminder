<?php

declare(strict_types=1);

namespace BirthdayReminder\Application\Command;

use BirthdayReminder\Application\Command\Exception\InvalidCommandFormat;

final class ParseResult
{
    /**
     * @param array<mixed, string> $matches
     */
    public function __construct(private readonly array $matches)
    {
    }

    public function get(string $key): string
    {
        return $this->matches[$key] ?? throw new InvalidCommandFormat();
    }
}
