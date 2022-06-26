<?php

declare(strict_types=1);

namespace BirthdayReminder\Observer;

use RuntimeException;
use Throwable;

final class ObserverAlreadyExists extends RuntimeException
{
    public function __construct(string $message = 'Observer already exists', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
