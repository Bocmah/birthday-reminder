<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use BirthdayReminder\Controller\TestController;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator
        ->services()
        ->defaults()
          ->autowire()
          ->autoconfigure();

    $services->set(TestController::class);
};
