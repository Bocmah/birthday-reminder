<?php

declare(strict_types=1);

namespace BirthdayReminder\Date;

use RuntimeException;
use Throwable;

final class InvalidDateFormat extends RuntimeException
{
    public function __construct(string $message = 'Incorrect date format', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
