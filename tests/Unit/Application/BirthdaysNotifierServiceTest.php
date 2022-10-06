<?php

declare(strict_types=1);

namespace Tests\Unit\Application;

use BirthdayReminder\Application\BirthdaysNotifierService;
use BirthdayReminder\Domain\BirthdaysNotifier\BirthdaysNotifier;
use BirthdayReminder\Domain\BirthdaysNotifier\BirthdaysNotifierSelector;
use BirthdayReminder\Domain\Observer\ObserverRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Domain\Observer\ObserverMother;

/**
 * @covers \BirthdayReminder\Application\BirthdaysNotifierService
 */
final class BirthdaysNotifierServiceTest extends TestCase
{
    private readonly BirthdaysNotifierService $notifierService;

    private readonly MockObject|ObserverRepository $observerRepository;

    private readonly MockObject|BirthdaysNotifierSelector $notifierSelector;

    /**
     * @test
     */
    public function notifyObservers(): void
    {
        $observers = [
            ObserverMother::createObserverWithOneObservee(),
            ObserverMother::createObserverWithOneObservee(),
        ];

        $this->observerRepository
            ->method('findAll')
            ->willReturn($observers);

        $notifierForFirstObserver = $this->createMock(BirthdaysNotifier::class);
        $notifierForFirstObserver
            ->expects($this->once())
            ->method('notify')
            ->with($observers[0]);

        $notifierForSecondObserver = $this->createMock(BirthdaysNotifier::class);
        $notifierForSecondObserver
            ->expects($this->once())
            ->method('notify')
            ->with($observers[1]);

        $this->notifierSelector
            ->method('selectNotifierForObserver')
            ->willReturnMap(
                [
                    [$observers[0], $notifierForFirstObserver],
                    [$observers[1], $notifierForSecondObserver]
                ]
            );

        $this->notifierService->notifyObservers();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->observerRepository = $this->createMock(ObserverRepository::class);
        $this->notifierSelector = $this->createMock(BirthdaysNotifierSelector::class);

        $this->notifierService = new BirthdaysNotifierService($this->observerRepository, $this->notifierSelector);
    }
}
