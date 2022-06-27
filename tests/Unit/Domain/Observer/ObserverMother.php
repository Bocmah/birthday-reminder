<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Observer;

use BirthdayReminder\Domain\FullName;
use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;

final class ObserverMother
{
    public static function createObserverWithoutObservees(): Observer
    {
        return new Observer(
            new UserId('123'),
            new FullName('John', 'Doe'),
        );
    }

    public static function createObserverWithOneObservee(): Observer
    {
        $observer = self::createObserverWithoutObservees();

        $observer->startObserving(
            new UserId('333'),
            new FullName('James', 'Dean'),
            new DateTimeImmutable('11.10.1990'),
        );

        return $observer;
    }
}
