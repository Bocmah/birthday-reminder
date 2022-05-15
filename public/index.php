<?php

declare(strict_types=1);

use Vkbd\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

/**
 * @var array<string, mixed> $context
 */
return static function (array $context): Kernel {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
