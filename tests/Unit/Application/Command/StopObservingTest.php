<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Command;

use BirthdayReminder\Application\Command\StopObserving;
use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\Observer\NotObservingUser;
use BirthdayReminder\Domain\Observer\ObserverWasNotFoundInTheSystem;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatableMessage;
use Tests\Unit\Domain\Observer\ObserverMother;

/**
 * @covers \BirthdayReminder\Application\Command\StopObserving
 */
final class StopObservingTest extends TestCase
{
    private const VALID_COMMAND = 'delete 333';

    /** @var MockObject&ObserverService */
    private MockObject $observerService;

    private StopObserving $command;

    /**
     * @test
     */
    public function stopObserving(): void
    {
        [$observerId, $observeeId] = ObserverMother::createObserverIdAndObserveeId();

        $this->observerService
            ->expects($this->once())
            ->method('stopObserving')
            ->with($observerId, $observeeId);

        $this->assertEquals(
            new TranslatableMessage('observee.stopped_observing', ['%id%' => (string) $observeeId]),
            $this->command->execute($observerId, self::VALID_COMMAND),
        );
    }

    /**
     * @test
     */
    public function observerWasNotFoundInTheSystem(): void
    {
        [$observerId, $observeeId] = ObserverMother::createObserverIdAndObserveeId();

        $this->observerService
            ->method('stopObserving')
            ->with($observerId, $observeeId)
            ->willThrowException(ObserverWasNotFoundInTheSystem::withUserId($observerId));

        $this->assertEquals(
            new TranslatableMessage('observee.not_observing', ['%id%' => (string) $observeeId]),
            $this->command->execute($observerId, self::VALID_COMMAND),
    );
    }

    /**
     * @test
     */
    public function notObservingUser(): void
    {
        [$observerId, $observeeId] = ObserverMother::createObserverIdAndObserveeId();

        $this->observerService
            ->method('stopObserving')
            ->with($observerId, $observeeId)
            ->willThrowException(NotObservingUser::withId($observerId));

        $this->assertEquals(
            new TranslatableMessage('observee.not_observing', ['%id%' => (string) $observeeId]),
            $this->command->execute($observerId, self::VALID_COMMAND),
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->observerService = $this->createMock(ObserverService::class);

        $this->command = new StopObserving($this->observerService);
    }
}
