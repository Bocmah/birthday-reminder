<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Observee;

use RuntimeException;
use Throwable;

final class ObserveeAlreadyExists extends RuntimeException
{
    public function __construct(string $message = 'Observee already exists', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
