<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Observer;

use BirthdayReminder\Domain\FullName;
use BirthdayReminder\Domain\Observee\Observee;
use BirthdayReminder\Domain\Observer\AlreadyObservingUser;
use BirthdayReminder\Domain\Observer\NotObservingUser;
use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BirthdayReminder\Domain\Observer\Observer
 */
final class ObserverTest extends TestCase
{
    private readonly UserId $observeeId;

    private readonly FullName $observeeFullName;

    private readonly DateTimeImmutable $observeeBirthdate;

    /**
     * @test
     */
    public function startObserving(): void
    {
        $observer = ObserverMother::createObserverWithoutObservees();

        $observer->startObserving($this->observeeId, $this->observeeFullName, $this->observeeBirthdate);

        $this->assertContainsEquals(
            new Observee($observer, $this->observeeId, $this->observeeFullName, $this->observeeBirthdate),
            $observer->observees(),
        );
    }

    /**
     * @test
     */
    public function canNotStartObservingIfAlreadyObserving(): void
    {
        $observer = ObserverMother::createObserverWithoutObservees();

        $observer->startObserving($this->observeeId, $this->observeeFullName, $this->observeeBirthdate);

        $this->expectException(AlreadyObservingUser::class);
        $this->expectExceptionMessage(sprintf('Already observing user with id %s', $this->observeeId));

        $observer->startObserving($this->observeeId, $this->observeeFullName, $this->observeeBirthdate);
    }

    /**
     * @test
     */
    public function stopObserving(): void
    {
        $observer = ObserverMother::createObserverWithoutObservees();

        $observer->startObserving($this->observeeId, $this->observeeFullName, $this->observeeBirthdate);

        $observee = new Observee($observer, $this->observeeId, $this->observeeFullName, $this->observeeBirthdate);

        $this->assertContainsEquals($observee, $observer->observees());

        $observer->stopObserving($this->observeeId);

        $this->assertNotContainsEquals($observee, $observer->observees());
    }

    /**
     * @test
     */
    public function canNotStopObservingNonExistentObservee(): void
    {
        $observer = ObserverMother::createObserverWithoutObservees();

        $this->expectException(NotObservingUser::class);
        $this->expectExceptionMessage(sprintf('Not observing user with id %s', $this->observeeId));

        $observer->stopObserving($this->observeeId);
    }

    /**
     * @test
     */
    public function changeObserveeBirthdate(): void
    {
        $observer = ObserverMother::createObserverWithoutObservees();

        $observer->startObserving($this->observeeId,$this->observeeFullName, $this->observeeBirthdate);

        $newBirthdate = new DateTimeImmutable('25.10.1990');

        $observer->changeObserveeBirthdate($this->observeeId, $newBirthdate);

        $observees = array_filter(
            $observer->observees(),
            fn (Observee $observee) => $observee->userId->equals($this->observeeId),
        );

        $this->assertCount(1, $observees);

        $observee = $observees[0];

        $this->assertEquals($newBirthdate, $observee->birthdate());
    }

    /**
     * @test
     */
    public function canNotChangeNonExistentObserveeBirthdate(): void
    {
        $observer = ObserverMother::createObserverWithoutObservees();

        $this->expectException(NotObservingUser::class);
        $this->expectExceptionMessage(sprintf('Not observing user with id %s', $this->observeeId));

        $observer->changeObserveeBirthdate($this->observeeId, new DateTimeImmutable('12.12.2012'));
    }

    /**
     * @test
     * @dataProvider upcomingBirthdaysProvider
     *
     * @param Observee[] $birthdays
     */
    public function upcomingBirthdays(Observer $observer, array $birthdays): void
    {
        $this->assertEquals($birthdays, $observer->upcomingBirthdays());
    }

    /**
     * @see upcomingBirthdays()
     *
     * @return iterable<string, array{observer: Observer, birthdays: Observee[]}>
     */
    public function upcomingBirthdaysProvider(): iterable
    {
        $observer = ObserverMother::createObserverWithoutObservees();

        $observeeWithBirthdayToday = ObserverMother::attachObservee($observer, id: new UserId('111'), birthdate: new DateTimeImmutable('today'));

        yield 'upcoming birthday today' => [
            'observer'  => $observer,
            'birthdays' => [$observeeWithBirthdayToday],
        ];

        $observer = ObserverMother::createObserverWithoutObservees();
        $observeeWithBirthdayToday = ObserverMother::attachObservee($observer, id: new UserId('111'), birthdate: new DateTimeImmutable('today'));
        $observeeWithBirthdayTomorrow = ObserverMother::attachObservee($observer, id: new UserId('222'), birthdate: new DateTimeImmutable('tomorrow'));

        yield 'upcoming birthdays today and tomorrow' => [
            'observer'  => $observer,
            'birthdays' => [$observeeWithBirthdayToday, $observeeWithBirthdayTomorrow],
        ];

        $observer = ObserverMother::createObserverWithoutObservees();
        $observeeWithBirthdayTomorrow = ObserverMother::attachObservee($observer, id: new UserId('222'), birthdate: new DateTimeImmutable('tomorrow'));

        yield 'upcoming birthday tomorrow' => [
            'observer'  => $observer,
            'birthdays' => [$observeeWithBirthdayTomorrow],
        ];

        $observer = ObserverMother::createObserverWithoutObservees();
        ObserverMother::attachObservee($observer, id: new UserId('333'), birthdate: new DateTimeImmutable('tomorrow +2days'));

        yield 'no upcoming birthdays' => [
            'observer'  => $observer,
            'birthdays' => [],
        ];
    }

    /**
     * @test
     */
    public function observerShouldAlwaysBeNotifiedByDefault(): void
    {
        $observer = ObserverMother::createObserverWithoutObservees();

        $this->assertTrue($observer->shouldAlwaysBeNotified());
    }

    /**
     * @test
     */
    public function toggleNotifiability(): void
    {
        $observer = ObserverMother::createObserverWithoutObservees();

        $this->assertTrue($observer->shouldAlwaysBeNotified());

        $observer->toggleNotifiability();

        $this->assertFalse($observer->shouldAlwaysBeNotified());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->observeeId = new UserId('333');
        $this->observeeFullName = new FullName('James', 'Dean');
        $this->observeeBirthdate = new DateTimeImmutable('10.12.1996');
    }
}