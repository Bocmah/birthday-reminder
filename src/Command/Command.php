<?php

declare(strict_types=1);

namespace Vkbd\Command;

use React\Promise\PromiseInterface;
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

    /**
     * @param IncomingMessage $message
     *
     * @return PromiseInterface<Response>
     */
    abstract public function execute(IncomingMessage $message): PromiseInterface;
}
