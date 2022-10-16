<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Util;

use JsonException;
use RuntimeException;

final class Json
{
    public static function decode(string $json): mixed
    {
        try {
            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Failed to decode JSON', 0, $exception);
        }
    }
}
