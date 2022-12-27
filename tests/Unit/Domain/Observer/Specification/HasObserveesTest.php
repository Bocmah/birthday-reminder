<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Observer\Specification;

use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\Observer\Specification\HasObservees;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Domain\Observer\ObserverMother;

/**
 * @covers \BirthdayReminder\Domain\Observer\Specification\HasObservees
 */
final class HasObserveesTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider isSatisfiedByProvider
     */
    public function isSatisfiedBy(Observer $observer, bool $result): void
    {
        $this->assertSame($result, (new HasObservees())->isSatisfiedBy($observer));
    }

    /**
     * @see isSatisfiedBy()
     *
     * @return iterable<int, array{0: Observer, 1: bool}>
     */
    public function isSatisfiedByProvider(): iterable
    {
        yield [ObserverMother::createObserverWithOneObservee(), true];

        yield [ObserverMother::createObserverWithoutObservees(), false];
    }
}
