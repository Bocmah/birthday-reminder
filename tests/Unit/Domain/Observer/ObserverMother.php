<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Observer;

use BirthdayReminder\Domain\FullName;
use BirthdayReminder\Domain\Observee\Observee;
use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;
use LogicException;
use Tests\Support\ObserveeData;
use Tests\Support\ObserverData;

final class ObserverMother
{
    public static function createObserverWithoutObservees(
        UserId $id = new UserId(ObserverData::ID),
        FullName $fullName = new FullName(ObserverData::FIRST_NAME, ObserverData::LAST_NAME),
        bool $shouldAlwaysBeNotified = true,
    ): Observer {
        $observer = new Observer($id, $fullName);

        if (!$shouldAlwaysBeNotified) {
            $observer->toggleNotifiability();
        }

        return $observer;
    }

    public static function createObserverWithOneObservee(): Observer
    {
        $observer = self::createObserverWithoutObservees();

        self::attachObservee($observer);

        return $observer;
    }

    public static function attachObservee(
        Observer $observer,
        UserId $id = new UserId(ObserveeData::ID),
        FullName $fullName = new FullName(ObserveeData::FIRST_NAME, ObserveeData::LAST_NAME),
        DateTimeImmutable $birthdate = new DateTimeImmutable(ObserveeData::BIRTHDATE),
    ): Observee {
        $observer->startObserving($id, $fullName, $birthdate);

        $last = array_key_last($observer->observees());

        if ($last === null) {
            throw new LogicException('Observees list is empty after adding an observee');
        }

        return $observer->observees()[$last];
    }

    public static function detachObservee(Observer $observer, UserId $id): void
    {
        $observer->stopObserving($id);
    }

    public static function createObserverId(): UserId
    {
        return new UserId(ObserverData::ID);
    }

    /**
     * @return array{0: UserId, 1: UserId}
     */
    public static function createObserverIdAndObserveeId(): array
    {
        return [self::createObserverId(), new UserId(ObserveeData::ID)];
    }
}
