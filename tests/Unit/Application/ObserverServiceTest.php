<?php

declare(strict_types=1);

namespace Tests\Unit\Application;

use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\FullName;
use BirthdayReminder\Domain\Observee\Observee;
use BirthdayReminder\Domain\Observee\ObserveeWasNotFoundOnThePlatform;
use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\Observer\ObserverWasNotFoundInTheSystem;
use BirthdayReminder\Domain\Observer\ObserverWasNotFoundOnThePlatform;
use BirthdayReminder\Domain\User\User;
use BirthdayReminder\Domain\User\UserFinder;
use BirthdayReminder\Domain\User\UserId;
use BirthdayReminder\Infrastructure\Persistence\Observer\InMemoryObserverRepository;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Domain\Observer\ObserverMother;

/**
 * @covers \BirthdayReminder\Application\ObserverService
 */
final class ObserverServiceTest extends TestCase
{
    private readonly ObserverService $observerService;

    private readonly InMemoryObserverRepository $observerRepository;

    /** @var MockObject&UserFinder */
    private readonly MockObject $userFinder;

    /**
     * @test
     */
    public function observeesOfObserverCanBeRetrieved(): void
    {
        $observer = ObserverMother::createObserverWithOneObservee();

        $this->givenObserverExists($observer);

        $observees = $this->observerService->getObservees($observer->id);

        $this->assertEquals($observer->observees(), $observees);
    }

    /**
     * @test
     */
    public function canNotRetrieveObserveesOfObserverIfObserverDoesNotExistInTheSystem(): void
    {
        $this->expectException(ObserverWasNotFoundInTheSystem::class);
        $this->expectExceptionMessage('Observer with user id non-existent-observer was not found in the system');

        $this->observerService->getObservees(new UserId('non-existent-observer'));
    }

    /**
     * @test
     */
    public function newObserverCanStartObserving(): void
    {
        $newObserver = ObserverMother::createObserverWithOneObservee();
        $newObservee = $newObserver->observees()[0];

        $this->givenUsersExist(
            new User($newObserver->id, $newObserver->fullName),
            new User($newObservee->userId, $newObservee->fullName)
        );

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

        $this->givenUsersExist(new User($newObservee->userId, $newObservee->fullName));

        $this->observerService->startObserving($existingObserver->id, $newObservee->userId, $newObservee->birthdate());

        $observer = $this->observerRepository->findByUserId($existingObserver->id);

        $this->assertInstanceOf(Observer::class, $observer);
        $this->assertCount(2, $observer->observees());
        $this->assertContainsEquals($newObservee, $observer->observees());
    }

    /**
     * @test
     */
    public function canNotStartObservingBecauseObserverWasNotFoundOnThePlatform(): void
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
    public function canNotStartObservingBecauseObserveeWasNotFoundOnThePlatform(): void
    {
        $observer = ObserverMother::createObserverWithoutObservees();

        $this->givenUsersExist(new User($observer->id, $observer->fullName));

        $this->expectExceptionMessage(ObserveeWasNotFoundOnThePlatform::class);
        $this->expectExceptionMessage('Observee with user id non-existent-observee was not found on the platform');

        $this->observerService->startObserving(
            $observer->id,
            new UserId('non-existent-observee'),
            new DateTimeImmutable('05.05.1990'),
        );
    }

    /**
     * @test
     */
    public function observerCanStopObserving(): void
    {
        $observer = ObserverMother::createObserverWithOneObservee();

        $this->givenObserverExists($observer);

        $observee = $observer->observees()[0];

        $observer->stopObserving($observee->userId);

        $observer = $this->observerRepository->findByUserId($observer->id);

        $this->assertNotNull($observer);

        $this->assertCount(0, $observer->observees());
    }

    /**
     * @test
     */
    public function canNotStopObservingBecauseObserverWasNotFoundInTheSystem(): void
    {
        $this->expectException(ObserverWasNotFoundInTheSystem::class);
        $this->expectExceptionMessage('Observer with user id non-existent-observer was not found in the system');

        $this->observerService->stopObserving(
            new UserId('non-existent-observer'),
            new UserId('observee-id'),
        );
    }

    /**
     * @test
     */
    public function observerCanChangeObserveeBirthdate(): void
    {
        $observer = ObserverMother::createObserverWithOneObservee();

        $this->givenObserverExists($observer);

        $observee = $observer->observees()[0];

        $newBirthdate = new DateTimeImmutable('15.10.1964');

        $this->observerService->changeObserveeBirthdate($observer->id, $observee->userId, $newBirthdate);

        $observer = $this->observerRepository->findByUserId($observer->id);

        $this->assertNotNull($observer);

        $this->assertEquals($newBirthdate, $observer->observees()[0]->birthdate());
    }

    /**
     * @test
     */
    public function canNotChangeObserveeBirthdateBecauseObserverWasNotFoundInTheSystem(): void
    {
        $this->expectException(ObserverWasNotFoundInTheSystem::class);
        $this->expectExceptionMessage('Observer with user id non-existent-observer was not found in the system');

        $this->observerService->changeObserveeBirthdate(
            new UserId('non-existent-observer'),
            new UserId('non-existent-observee'),
            new DateTimeImmutable('10.10.1990'),
        );
    }

    /**
     * @test
     */
    public function observerCanToggleNotifiability(): void
    {
        $observer = ObserverMother::createObserverWithOneObservee();

        $this->givenObserverExists($observer);

        $this->assertTrue($observer->shouldAlwaysBeNotified());

        $this->observerService->toggleNotifiability($observer->id);

        $observer = $this->observerRepository->findByUserId($observer->id);

        $this->assertNotNull($observer);

        $this->assertFalse($observer->shouldAlwaysBeNotified());
    }

    /**
     * @test
     */
    public function canNotToggleNotifiabilityBecauseObserverWasNotFoundInTheSystem(): void
    {
        $this->expectException(ObserverWasNotFoundInTheSystem::class);
        $this->expectExceptionMessage('Observer with user id non-existent-observer was not found in the system');

        $this->observerService->toggleNotifiability(new UserId('non-existent-observer'));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->observerRepository = new InMemoryObserverRepository();
        $this->userFinder = $this->createMock(UserFinder::class);

        $this->observerService = new ObserverService($this->observerRepository, $this->userFinder);
    }

    private function givenUsersExist(User ...$users): void
    {
        $calls = [];

        foreach ($users as $user) {
            $calls[] = [$user->id, $user];
        }

        $this->userFinder
            ->method('findById')
            ->willReturnMap($calls);
    }

    private function givenObserverExists(Observer $observer): void
    {
        $this->observerRepository->save($observer);
    }
}
