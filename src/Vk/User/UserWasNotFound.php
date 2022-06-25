<?php

declare(strict_types=1);

namespace BirthdayReminder\Vk\User;

use Throwable;

final class UserWasNotFound extends FailedToRetrieveUser
{
    public function __construct(string $message = 'User was not found', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
