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

    /**
     * @test
     * @dataProvider birthdaysOnDateProvider
     *
     * @param Observee[] $birthdays
     */
    public function birthdaysOnDate(Observer $observer, DateTimeImmutable $date, array $birthdays): void
    {
        $this->assertEquals($birthdays, $observer->birthdaysOnDate($date));
    }

    public function birthdaysOnDateProvider(): iterable
    {
        $observer = ObserverMother::createObserverWithoutObservees();

        $observee1 = ObserverMother::attachObservee($observer, id: new UserId('111'), birthdate: new DateTimeImmutable('17.10.1996'));
        $observee2 = ObserverMother::attachObservee($observer, id: new UserId('222'), birthdate: new DateTimeImmutable('05.04.2000'));
        $observee3 = ObserverMother::attachObservee($observer, id: new UserId('333'), birthdate: new DateTimeImmutable('05.04.2000'));

        yield 'one birthday on date' => [
            $observer,
            new DateTimeImmutable('17.10.2022'),
            [$observee1],
        ];

        yield 'two birthdays on date' => [
            $observer,
            new DateTimeImmutable('05.04.2022'),
            [$observee2, $observee3],
        ];

        yield 'no birthdays on date' => [
            $observer,
            new DateTimeImmutable('01.01.2022'),
            [],
        ];

        yield 'time should be ignored' => [
            $observer,
            new DateTimeImmutable('17.10.2022 23:58'),
            [$observee1],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->observeeId = new UserId('333');
        $this->observeeFullName = new FullName('James', 'Dean');
        $this->observeeBirthdate = new DateTimeImmutable('10.12.1996');
    }
}
