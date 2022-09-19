<?php

declare(strict_types=1);

namespace BirthdayReminder\Application\Command;

final class ParseResult
{
    /**
     * @param array<string, mixed> $matches
     */
    public function __construct(private readonly array $matches)
    {
    }

    public function get(string $key): mixed
    {
        return $this->matches[$key] ?? throw new InvalidCommandFormat();
    }
}
