<?php

declare(strict_types=1);

use BirthdayReminder\Infrastructure\Http\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

/**
 * @var array{APP_ENV: string, APP_DEBUG: bool|int|string} $context
 */
return static function (array $context): Kernel {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
