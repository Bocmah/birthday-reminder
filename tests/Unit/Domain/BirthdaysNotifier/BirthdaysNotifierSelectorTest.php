<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\BirthdaysNotifier;

use BirthdayReminder\Domain\BirthdaysNotifier\BirthdaysNotifier;
use BirthdayReminder\Domain\BirthdaysNotifier\BirthdaysNotifierSelector;
use LogicException;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Domain\Observer\ObserverMother;

/**
 * @covers \BirthdayReminder\Domain\BirthdaysNotifier\BirthdaysNotifierSelector
 */
final class BirthdaysNotifierSelectorTest extends TestCase
{
    /**
     * @test
     */
    public function selectsNotifierWhichCanNotifyObserver(): void
    {
        $observer = ObserverMother::createObserverWithOneObservee();

        $notifier1 = $this->createMock(BirthdaysNotifier::class);
        $notifier2 = $this->createMock(BirthdaysNotifier::class);

        $notifier1
            ->method('canNotify')
            ->with($observer)
            ->willReturn(false);
        $notifier2
            ->method('canNotify')
            ->with($observer)
            ->willReturn(true);

        $selector = new BirthdaysNotifierSelector([$notifier1, $notifier2]);

        $this->assertEquals($notifier2, $selector->selectNotifierForObserver($observer));
    }

    /**
     * @test
     */
    public function selectsFirstNotifierWhichCanNotifyObserver(): void
    {
        $observer = ObserverMother::createObserverWithOneObservee();

        $notifier1 = $this->createMock(BirthdaysNotifier::class);
        $notifier2 = $this->createMock(BirthdaysNotifier::class);

        $notifier1
            ->method('canNotify')
            ->with($observer)
            ->willReturn(true);
        $notifier2
            ->method('canNotify')
            ->with($observer)
            ->willReturn(true);

        $selector = new BirthdaysNotifierSelector([$notifier1, $notifier2]);

        $this->assertEquals($notifier1, $selector->selectNotifierForObserver($observer));
    }

    /**
     * @test
     */
    public function failsWhenNotifierWasNotFound(): void
    {
        $observer = ObserverMother::createObserverWithOneObservee();

        $notifier1 = $this->createMock(BirthdaysNotifier::class);

        $notifier1
            ->method('canNotify')
            ->with($observer)
            ->willReturn(false);

        $selector = new BirthdaysNotifierSelector([$notifier1]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('No notifier was found for observer');

        $selector->selectNotifierForObserver($observer);
    }
}
