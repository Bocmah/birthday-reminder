<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Observee;

use BirthdayReminder\Domain\Observee\Observee;
use BirthdayReminder\Domain\Observee\ObserveeFormatter;

final class VkObserveeFormatter implements ObserveeFormatter
{
    private const TEMPLATE = '*id%s (%s %s)';

    public function format(Observee $observee): string
    {
        return sprintf(
            self::TEMPLATE,
            (string) $observee->userId,
            $observee->fullName->firstName,
            $observee->fullName->lastName,
        );
    }
}
