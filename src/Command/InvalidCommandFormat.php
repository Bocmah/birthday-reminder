<?php

declare(strict_types=1);

namespace Vkbd\Command;

use RuntimeException;
use Throwable;

final class InvalidCommandFormat extends RuntimeException
{
    public function __construct(string $message = 'Incorrect command format', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
