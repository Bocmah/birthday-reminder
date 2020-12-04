<?php

declare(strict_types=1);

namespace Vkbd\Vk;

use InvalidArgumentException;

final class Event
{
    public const CONFIRMATION = 'confirmation';

    private string $type;

    public function __construct(string $type)
    {
        if ($type !== self::CONFIRMATION) {
            throw new InvalidArgumentException('Unknown event type');
        }

        $this->type = $type;
    }


    public function __toString(): string
    {
        return $this->type;
    }
}
