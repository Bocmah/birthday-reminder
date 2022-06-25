<?php

declare(strict_types=1);

use BirthdayReminder\Date\InvalidDateFormat;
use BirthdayReminder\Vk\User\Id\AlphanumericVkId;
use BirthdayReminder\Vk\User\Id\NumericVkId;

if (!function_exists('extract_date')) {
    function extract_date(string $rawDate, string $dateFormat = 'd.m.Y|', string $dateSeparator = '.'): DateTimeImmutable
    {
        $splitDate = explode($dateSeparator, $rawDate);

        if (count($splitDate) !== 3) {
            throw new InvalidDateFormat();
        }

        [$day, $month, $year] = $splitDate;

        if (!checkdate((int) $month, (int) $day, (int) $year)) {
            throw new InvalidDateFormat();
        }

        $date = DateTimeImmutable::createFromFormat($dateFormat, $rawDate);

        if ($date === false) {
            throw new InvalidDateFormat();
        }

        return $date;
    }
}

if (!function_exists('extract_id')) {
    function extract_id(string $rawId): NumericVkId|AlphanumericVkId
    {
        if (is_numeric($rawId)) {
            return new NumericVkId((int) $rawId);
        }

        return new AlphanumericVkId($rawId);
    }
}
