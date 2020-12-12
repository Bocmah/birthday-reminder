<?php

declare(strict_types=1);

namespace Vkbd\Command;

final class Definition
{
    private string $pattern;

    /** @var callable */
    private $handler;

    public function __construct(string $pattern, callable $handler)
    {
        $this->pattern = $pattern;
        $this->handler = $handler;
    }

    public function matches(string $value): bool
    {
        return (bool) preg_match($this->pattern, $value);
    }

    public function handler(): callable
    {
        return $this->handler;
    }
}
