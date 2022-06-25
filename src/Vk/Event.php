<?php

declare(strict_types=1);

namespace BirthdayReminder\Vk;

use InvalidArgumentException;
use Stringable;

final class Event implements Stringable
{
    public const CONFIRMATION = 'confirmation';

    public const NEW_MESSAGE = 'message_new';

    private string $type;

    public function __construct(string $type)
    {
        if ($type !== self::CONFIRMATION && $type !== self::NEW_MESSAGE) {
            throw new InvalidArgumentException('Unknown event type');
        }

        $this->type = $type;
    }

    public function is(string $event): bool
    {
        return $this->type === $event;
    }

    public function __toString(): string
    {
        return $this->type;
    }
}
