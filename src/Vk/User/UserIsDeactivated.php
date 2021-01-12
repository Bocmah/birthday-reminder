<?php

declare(strict_types=1);

namespace Vkbd\Vk\User;

use Throwable;

final class UserIsDeactivated extends FailedToRetrieveUser
{
    public function __construct(string $message = 'User is deactivated', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
