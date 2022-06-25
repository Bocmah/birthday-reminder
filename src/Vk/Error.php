<?php

declare(strict_types=1);

namespace BirthdayReminder\Vk;

use InvalidArgumentException;

final class Error
{
    public const INVALID_USER_ID = 113;

    private int $code;

    public function __construct(int $code)
    {
        if ($code !== self::INVALID_USER_ID) {
            throw new InvalidArgumentException('Unknown error code');
        }

        $this->code = $code;
    }

    public function is(int $code): bool
    {
        return $this->code === $code;
    }
}
