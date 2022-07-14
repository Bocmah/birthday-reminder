<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Observee;

interface ObserveeFormatter
{
    public function format(Observee $observee): string;
}
