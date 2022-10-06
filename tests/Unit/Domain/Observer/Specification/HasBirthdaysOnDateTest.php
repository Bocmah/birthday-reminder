<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Observer\Specification;

use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\Observer\Specification\HasBirthdaysOnDate;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Domain\Observer\ObserverMother;

/**
 * @covers \BirthdayReminder\Domain\Observer\Specification\HasBirthdaysOnDate
 */
final class HasBirthdaysOnDateTest extends TestCase
{
    /**
     * @test
     * @dataProvider isSatisfiedByProvider
     *
     * @param callable():Observer $createObserver
     */
    public function isSatisfiedBy(callable $createObserver, DateTimeImmutable $date, bool $result): void
    {
        $this->assertSame($result, (new HasBirthdaysOnDate($date))->isSatisfiedBy($createObserver()));
    }

    /**
     * @see isSatisfiedBy()
     *
     * @return iterable<int, array{createObserver: callable():Observer, date: DateTimeImmutable, result: bool}>
     */
    public function isSatisfiedByProvider(): iterable
    {
        yield [
            'createObserver' => static function (): Observer {
                $observer = ObserverMother::createObserverWithoutObservees();
                ObserverMother::attachObservee($observer, birthdate: new DateTimeImmutable('15.10.2000'));

                return $observer;
            },
            'date' => new DateTimeImmutable('15.10.2022'),
            'result' => true,
        ];

        yield [
            'createObserver' => static function (): Observer {
                $observer = ObserverMother::createObserverWithoutObservees();
                ObserverMother::attachObservee($observer, birthdate: new DateTimeImmutable('15.10.2000'));

                return $observer;
            },
            'date' => new DateTimeImmutable('16.10.2022'),
            'result' => false,
        ];
    }
}
