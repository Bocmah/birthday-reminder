<?php

declare(strict_types=1);

namespace Vkbd\Vk\User;

use Throwable;

final class UnknownError extends FailedToRetrieveUser
{
    public function __construct(string $message = 'Unknown error.', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
