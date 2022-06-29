<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Observer;

use BirthdayReminder\Domain\FullName;
use BirthdayReminder\Domain\Observee\Observee;
use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;

final class ObserverMother
{
    public static function createObserverWithoutObservees(
        UserId $id = new UserId('123'),
        FullName $fullName = new FullName('John', 'Doe')
    ): Observer {
        return new Observer($id, $fullName);
    }

    public static function createObserverWithOneObservee(): Observer
    {
        $observer = self::createObserverWithoutObservees();

        self::attachObservee($observer);

        return $observer;
    }

    public static function attachObservee(
        Observer $observer,
        UserId $id = new UserId('333'),
        FullName $fullName = new FullName('James', 'Dean'),
        DateTimeImmutable $birthdate = new DateTimeImmutable('11.10.1990'),
    ): Observee {
        $observer->startObserving($id, $fullName, $birthdate);

        return $observer->observees()[count($observer->observees()) - 1];
    }
}
