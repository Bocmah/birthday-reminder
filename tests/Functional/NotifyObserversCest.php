<?php

declare(strict_types=1);

namespace Tests\Functional;

use Tests\Codeception\Support\FunctionalTester;
use Tests\Support\ObserverData;

final class NotifyObserversCest
{
    // These dates are used by FixedCalendar
    private const FIXED_DATE_TODAY = '21.01.2023';

    private const FIXED_DATE_TOMORROW = '22.01.2023';

    private const FIXED_DATE_AFTER_TOMORROW = '23.01.2023';

    public function successfulNotification(FunctionalTester $tester): void
    {
        $tester->haveObserver([
            'observees' => [
                [
                    'userId' => '333',
                    'fullName' => [
                        'firstName' => 'John',
                        'lastName' => 'Doe',
                    ],
                    'birthdate' => self::FIXED_DATE_TODAY,
                ],
                [
                    'userId' => '444',
                    'fullName' => [
                        'firstName' => 'Kate',
                        'lastName' => 'Watts',
                    ],
                    'birthdate' => self::FIXED_DATE_TODAY,
                ],
                [
                    'userId' => '555',
                    'fullName' => [
                        'firstName' => 'James',
                        'lastName' => 'Dean',
                    ],
                    'birthdate' => self::FIXED_DATE_TOMORROW,
                ],
            ],
        ]);
        $tester->haveObserver([
            '_id' => '777',
            'observees' => [
                [
                    'userId' => '888',
                    'fullName' => [
                        'firstName' => 'Jane',
                        'lastName' => 'Wales',
                    ],
                    'birthdate' => self::FIXED_DATE_TOMORROW,
                ],
            ],
        ]);

        $tester->runShellCommand('php bin/console observers:notify');

        $tester->seeMessageContainingTextWasSentToUser(
            'Сегодня дни рождения у этих людей:\n\n*id333 (John Doe)\n*id444 (Kate Watts)\n\nЗавтра дни рождения у этих людей:\n\n*id555 (James Dean)',
            ObserverData::ID,
        );
        $tester->seeMessageContainingTextWasSentToUser(
            'Завтра дни рождения у этих людей:\n\n*id888 (Jane Wales)',
            '777',
        );
    }

    public function observerWithoutObserveesMustNotBeNotified(FunctionalTester $tester): void
    {
        $tester->haveObserver();

        $tester->runShellCommand('php bin/console observers:notify');

        $tester->seeNoMessagesWereSent();
    }

    public function observerWithNoUpcomingBirthdaysButWithDisabledNotificationsMustNotBeNotified(FunctionalTester $tester): void
    {
        $tester->haveObserver(['shouldAlwaysBeNotified' => false]);

        $tester->runShellCommand('php bin/console observers:notify');

        $tester->seeNoMessagesWereSent();
    }

    public function observerWithNoUpcomingBirthdaysButWithEnabledNotificationsMustBeNotified(FunctionalTester $tester): void
    {
        $tester->haveObserver([
            'observees' => [
                [
                    'birthdate' => self::FIXED_DATE_AFTER_TOMORROW,
                ],
            ],
        ]);

        $tester->runShellCommand('php bin/console observers:notify');

        $tester->seeMessageContainingTextWasSentToUser(
            'Сегодня и завтра дней рождения не предвидится.',
            ObserverData::ID,
        );
    }
}
