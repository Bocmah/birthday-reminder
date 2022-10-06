<?php

declare(strict_types=1);

namespace BirthdayReminder\Application;

use BirthdayReminder\Domain\Observee\Observee;
use BirthdayReminder\Domain\Observee\ObserveeWasNotFoundOnThePlatform;
use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\Observer\ObserverRepository;
use BirthdayReminder\Domain\Observer\ObserverWasNotFoundInTheSystem;
use BirthdayReminder\Domain\Observer\ObserverWasNotFoundOnThePlatform;
use BirthdayReminder\Domain\User\User;
use BirthdayReminder\Domain\User\UserFinder;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;

class ObserverService
{
    public function __construct(
        private readonly ObserverRepository $observerRepository,
        private readonly UserFinder         $userFinder,
    ) {
    }

    /**
     * @return Observee[]
     */
    public function getObservees(UserId $observerId): array
    {
        return $this->findObserverInTheSystem($observerId)->observees();
    }

    public function startObserving(UserId $observerId, UserId $observeeId, DateTimeImmutable $observeeBirthdate): void
    {
        $observer = $this->findObserverInTheSystemOrOnThePlatform($observerId);

        $user = $this->findObserveeOnThePlatform($observeeId);

        $observer->startObserving($user->id, $user->fullName, $observeeBirthdate);

        $this->observerRepository->save($observer);
    }

    public function stopObserving(UserId $observerId, UserId $observeeId): void
    {
        $observer = $this->findObserverInTheSystem($observerId);

        $observer->stopObserving($observeeId);

        $this->observerRepository->save($observer);
    }

    public function changeObserveeBirthdate(UserId $observerId, UserId $observeeId, DateTimeImmutable $newBirthdate): void
    {
        $observer = $this->findObserverInTheSystem($observerId);

        $observer->changeObserveeBirthdate($observeeId, $newBirthdate);

        $this->observerRepository->save($observer);
    }

    public function toggleNotifiability(UserId $observerId): void
    {
        $observer = $this->findObserverInTheSystem($observerId);

        $observer->toggleNotifiability();

        $this->observerRepository->save($observer);
    }

    private function findObserverInTheSystemOrOnThePlatform(UserId $id): Observer
    {
        $observer = $this->observerRepository->findByUserId($id);

        if ($observer === null) {
            $user = $this->findObserverOnThePlatform($id);

            $observer = new Observer($user->id, $user->fullName);
        }

        return $observer;
    }

    private function findObserverInTheSystem(UserId $id): Observer
    {
        $observer = $this->observerRepository->findByUserId($id);

        if ($observer === null) {
            throw ObserverWasNotFoundInTheSystem::withUserId($id);
        }

        return $observer;
    }

    private function findObserverOnThePlatform(UserId $id): User
    {
        $user = $this->userFinder->findById($id);

        if ($user === null) {
            throw ObserverWasNotFoundOnThePlatform::withUserId($id);
        }

        return $user;
    }

    private function findObserveeOnThePlatform(UserId $id): User
    {
        $user = $this->userFinder->findById($id);

        if ($user === null) {
            throw ObserveeWasNotFoundOnThePlatform::withUserId($id);
        }

        return $user;
    }
}
