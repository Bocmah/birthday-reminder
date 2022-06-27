<?php

declare(strict_types=1);

namespace Tests\Unit\Application;

use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\FullName;
use BirthdayReminder\Domain\Observee\Observee;
use BirthdayReminder\Domain\Observee\ObserveeWasNotFoundOnThePlatform;
use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\Observer\ObserverWasNotFoundOnThePlatform;
use BirthdayReminder\Domain\User\User;
use BirthdayReminder\Domain\User\UserId;
use BirthdayReminder\Infrastructure\Api\User\InMemoryUserFinder;
use BirthdayReminder\Infrastructure\Persistence\Observer\InMemoryObserverRepository;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Domain\Observer\ObserverMother;

final class ObserverServiceTest extends TestCase
{
    private readonly ObserverService $observerService;

    private readonly InMemoryObserverRepository $observerRepository;

    private readonly InMemoryUserFinder $userFinder;

    /**
     * @test
     */
    public function newObserverCanStartObserving(): void
    {
        $newObserver = ObserverMother::createObserverWithOneObservee();
        $newObservee = $newObserver->observees()[0];

        $this->givenUserExists(new User($newObserver->id, $newObserver->fullName));
        $this->givenUserExists(new User($newObservee->userId, $newObservee->fullName));

        $this->observerService->startObserving(
            $newObserver->id,
            $newObservee->userId,
            $newObservee->birthdate(),
        );

        $observer = $this->observerRepository->findByUserId($newObserver->id);

        $this->assertEquals($newObserver, $observer);
    }

    /**
     * @test
     */
    public function existingObserverCanStartObserving(): void
    {
        $existingObserver = ObserverMother::createObserverWithOneObservee();

        $this->givenObserverExists($existingObserver);

        $newObservee = new Observee(
            $existingObserver,
            new UserId('777'),
            new FullName('Jane', 'Watts'),
            new DateTimeImmutable('05.10.1977'),
        );

        $this->givenUserExists(new User($newObservee->userId, $newObservee->fullName));

        $this->observerService->startObserving($existingObserver->id, $newObservee->userId, $newObservee->birthdate());

        $observer = $this->observerRepository->findByUserId($existingObserver->id);

        $this->assertInstanceOf(Observer::class, $observer);
        $this->assertCount(2, $observer->observees());
        $this->assertContainsEquals($newObservee, $observer->observees());
    }

    /**
     * @test
     */
    public function exceptionIsThrownWhenObserverWasNotFoundOnThePlatform(): void
    {
        $this->expectException(ObserverWasNotFoundOnThePlatform::class);
        $this->expectExceptionMessage('Observer with user id non-existent-observer was not found on the platform');

        $this->observerService->startObserving(
            new UserId('non-existent-observer'),
            new UserId('existent-observee'),
            new DateTimeImmutable('10.07.1976'),
        );
    }

    /**
     * @test
     */
    public function exceptionIsThrownWhenObserveeWasNotFoundOnThePlatform(): void
    {
        $observer = ObserverMother::createObserverWithoutObservees();

        $this->givenUserExists(new User($observer->id, $observer->fullName));

        $this->expectExceptionMessage(ObserveeWasNotFoundOnThePlatform::class);
        $this->expectExceptionMessage('Observee with user id non-existent-observee was not found on the platform');

        $this->observerService->startObserving(
            $observer->id,
            new UserId('non-existent-observee'),
            new DateTimeImmutable('05.05.1990'),
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->observerRepository = new InMemoryObserverRepository();
        $this->userFinder = new InMemoryUserFinder();

        $this->observerService = new ObserverService($this->observerRepository, $this->userFinder);
    }

    private function givenUserExists(User $user): void
    {
        $this->userFinder->addUser($user);
    }

    private function givenObserverExists(Observer $observer): void
    {
        $this->observerRepository->save($observer);
    }
}
