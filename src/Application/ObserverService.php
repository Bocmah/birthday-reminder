<?php

declare(strict_types=1);

namespace BirthdayReminder\Application;

use BirthdayReminder\Domain\Observee\ObserveeWasNotFoundOnThePlatform;
use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\Observer\ObserverRepository;
use BirthdayReminder\Domain\Observer\ObserverWasNotFoundInTheSystem;
use BirthdayReminder\Domain\Observer\ObserverWasNotFoundOnThePlatform;
use BirthdayReminder\Domain\User\User;
use BirthdayReminder\Domain\User\UserFinder;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;

final class ObserverService
{
    public function __construct(
        private readonly ObserverRepository $observerRepository,
        private readonly UserFinder         $userFinder,
    ) {
    }

    public function startObserving(UserId $observerId, UserId $observeeId, DateTimeImmutable $observeeBirthdate): void
    {
        $observer = $this->observerRepository->findByUserId($observerId);

        if ($observer === null) {
            $user = $this->findObserverOnThePlatform($observerId);

            $observer = new Observer($user->id, $user->fullName);
        }

        $user = $this->findObserveeOnThePlatform($observeeId);

        $observer->startObserving($user->id, $user->fullName, $observeeBirthdate);

        $this->observerRepository->save($observer);
    }

    public function stopObserving(UserId $observerId, UserId $observeeId): void
    {
        $observer = $this->observerRepository->findByUserId($observerId);

        if ($observer === null) {
            throw ObserverWasNotFoundInTheSystem::withUserId($observerId);
        }

        $observer->stopObserving($observeeId);

        $this->observerRepository->save($observer);
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
