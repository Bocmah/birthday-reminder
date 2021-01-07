<?php

declare(strict_types=1);

namespace Vkbd\Command;

use Vkbd\Vk\Message\IncomingMessage;

abstract class Command
{
    private string $pattern;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    public function matches(string $value): bool
    {
        return (bool) preg_match($this->pattern, $value);
    }

    abstract public function execute(IncomingMessage $message): Response;
}
