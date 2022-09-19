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
    private const OBSERVER_ID = '123';

    private const OBSERVEE_ID = '333';

    public static function createObserverWithoutObservees(
        UserId $id = new UserId(self::OBSERVER_ID),
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
        UserId $id = new UserId(self::OBSERVEE_ID),
        FullName $fullName = new FullName('James', 'Dean'),
        DateTimeImmutable $birthdate = new DateTimeImmutable('11.10.1990'),
    ): Observee {
        $observer->startObserving($id, $fullName, $birthdate);

        return $observer->observees()[count($observer->observees()) - 1];
    }

    public static function detachObservee(Observer $observer, UserId $id): void
    {
        $observer->stopObserving($id);
    }

    public static function createObserverId(): UserId
    {
        return new UserId(self::OBSERVER_ID);
    }

    /**
     * @return array{0: UserId, 1: UserId}
     */
    public static function createObserverIdAndObserveeId(): array
    {
        return [self::createObserverId(), new UserId(self::OBSERVEE_ID)];
    }
}
