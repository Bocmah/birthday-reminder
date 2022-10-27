<?php

declare(strict_types=1);

use BirthdayReminder\Application\BirthdaysNotifierService;
use BirthdayReminder\Application\Command\ChangeBirthdate;
use BirthdayReminder\Application\Command\Command;
use BirthdayReminder\Application\Command\CommandSelector;
use BirthdayReminder\Application\Command\StartObserving;
use BirthdayReminder\Application\Command\StopObserving;
use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\BirthdaysNotifier\BirthdaysNotifierSelector;
use BirthdayReminder\Domain\BirthdaysNotifier\NoUpcomingBirthdaysNotifier;
use BirthdayReminder\Domain\BirthdaysNotifier\NullBirthdaysNotifier;
use BirthdayReminder\Domain\BirthdaysNotifier\UpcomingBirthdaysNotifier;
use BirthdayReminder\Domain\Date\Calendar;
use BirthdayReminder\Domain\Messenger\Messenger;
use BirthdayReminder\Domain\Observee\ObserveeFormatter;
use BirthdayReminder\Domain\Observer\ObserverRepository;
use BirthdayReminder\Domain\User\UserFinder;
use BirthdayReminder\Infrastructure\Api\Messenger\BatchMessengerDecorator;
use BirthdayReminder\Infrastructure\Api\Messenger\VkMessenger;
use BirthdayReminder\Infrastructure\Api\User\VkUserFinder;
use BirthdayReminder\Infrastructure\Api\Vk\VkApi;
use BirthdayReminder\Infrastructure\Controller\MessageReceiver;
use BirthdayReminder\Infrastructure\Date\SystemCalendar;
use BirthdayReminder\Infrastructure\Observee\VkObserveeFormatter;
use BirthdayReminder\Infrastructure\Persistence\Observer\DoctrineMongoDBObserverRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $configurator) {
    $configurator
        ->parameters()
        ->set('vk.max_message_length', 4096);

    $services = $configurator
        ->services()
        ->defaults()
          ->autowire()
          ->autoconfigure();

    $services->instanceof(Command::class)->tag('command');

    $services->set(StartObserving::class);
    $services->set(StopObserving::class);
    $services->set(ChangeBirthdate::class);

    $services->set(CommandSelector::class)->args([tagged_iterator('command')]);

    $services->set(UpcomingBirthdaysNotifier::class)->tag('birthdays_notifier');
    $services->set(NoUpcomingBirthdaysNotifier::class)->tag('birthdays_notifier');
    $services->set(NullBirthdaysNotifier::class)->tag('birthdays_notifier', ['priority' => -1]);

    $services->set(BirthdaysNotifierSelector::class)->args([tagged_iterator('birthdays_notifier')]);

    $services->set(BirthdaysNotifierService::class);

    $services->set(ObserverService::class);

    $services->set(DoctrineMongoDBObserverRepository::class);

    $services->alias(ObserverRepository::class, DoctrineMongoDBObserverRepository::class);

    $services->set(VkObserveeFormatter::class);

    $services->alias(ObserveeFormatter::class, VkObserveeFormatter::class);

    $services->set(VkApi::class);
    $services->set(VkUserFinder::class);

    $services->alias(UserFinder::class, VkUserFinder::class);

    $services->set(VkMessenger::class);

    $services->alias(Messenger::class, VkMessenger::class);

    $services->set(MessageReceiver::class);

    $services->set(SystemCalendar::class);

    $services->alias(Calendar::class, SystemCalendar::class);

    $services->set(\BirthdayReminder\Infrastructure\Date\IntlDateFormatter::class);

    $services
        ->set(BatchMessengerDecorator::class)
        ->decorate(VkMessenger::class)
        ->args([service('.inner'), param('vk.max_message_length')]);
};
