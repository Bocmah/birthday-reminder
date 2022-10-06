<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Observer\Specification;

use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\Observer\Specification\ShouldAlwaysBeNotified;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Domain\Observer\ObserverMother;

/**
 * @covers \BirthdayReminder\Domain\Observer\Specification\ShouldAlwaysBeNotified
 */
final class ShouldAlwaysBeNotifiedTest extends TestCase
{
    /**
     * @test
     * @dataProvider isSatisfiedByProvider
     */
    public function isSatisfiedBy(Observer $observer, bool $result): void
    {
        $this->assertSame($result, (new ShouldAlwaysBeNotified())->isSatisfiedBy($observer));
    }

    /**
     * @see isSatisfiedBy()
     *
     * @return iterable<int, array{0: Observer, 1: bool}>
     */
    public function isSatisfiedByProvider(): iterable
    {
        yield [ObserverMother::createObserverWithoutObservees(), true];

        $nonNotifiableObserver = ObserverMother::createObserverWithoutObservees();
        $nonNotifiableObserver->toggleNotifiability();

        yield [$nonNotifiableObserver, false];
    }
}
