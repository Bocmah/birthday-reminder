<?php

declare(strict_types=1);

use BirthdayReminder\Infrastructure\Controller\MessageReceiver;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator
        ->services()
        ->defaults()
          ->autowire()
          ->autoconfigure();

    $services->set(MessageReceiver::class);
};
