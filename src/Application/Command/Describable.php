<?php

declare(strict_types=1);

namespace BirthdayReminder\Application\Command;

use Symfony\Contracts\Translation\TranslatableInterface;

interface Describable
{
    public function name(): string;

    public function description(): TranslatableInterface;
}
