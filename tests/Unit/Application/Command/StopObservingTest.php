<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Command;

use BirthdayReminder\Application\Command\StopObserving;
use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\Observer\NotObservingUser;
use BirthdayReminder\Domain\Observer\ObserverWasNotFoundInTheSystem;
use BirthdayReminder\Domain\User\UserId;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \BirthdayReminder\Application\Command\StopObserving
 */
final class StopObservingTest extends CommandTestCase
{
    private const VALID_COMMAND = 'delete 333';

    private MockObject|ObserverService $observerService;

    private StopObserving $command;

    /**
     * @test
     */
    public function stopObserving(): void
    {
        $this->observerService
            ->expects($this->once())
            ->method('stopObserving')
            ->with(new UserId(self::OBSERVER_ID), new UserId(self::OBSERVEE_ID));

        $message = sprintf('Вы больше не следите за днем рождения пользователя с id %s.', self::OBSERVEE_ID);

        $this->translator
            ->method('trans')
            ->with('observee.stopped_observing', ['%id%' => self::OBSERVEE_ID])
            ->willReturn($message);

        $this->expectMessageToObserver($message);

        $this->command->execute(new UserId(self::OBSERVER_ID), self::VALID_COMMAND);
    }

    /**
     * @test
     */
    public function informsObserverAboutNotObservingObserveeWhenObserverWasNotFoundInTheSystem(): void
    {
        $this->observerService
            ->method('stopObserving')
            ->with(new UserId(self::OBSERVER_ID), new UserId(self::OBSERVEE_ID))
            ->willThrowException(ObserverWasNotFoundInTheSystem::withUserId(new UserId(self::OBSERVER_ID)));

        $message = sprintf('Вы не следите за днем рождения пользователя с id %s.', self::OBSERVEE_ID);

        $this->translator
            ->method('trans')
            ->with('observee.not_observing', ['%id%' => self::OBSERVEE_ID])
            ->willReturn($message);

        $this->expectMessageToObserver($message);

        $this->command->execute(new UserId(self::OBSERVER_ID), self::VALID_COMMAND);
    }

    /**
     * @test
     */
    public function informsObserverWhenNotObservingUser(): void
    {
        $this->observerService
            ->method('stopObserving')
            ->with(new UserId(self::OBSERVER_ID), new UserId(self::OBSERVEE_ID))
            ->willThrowException(NotObservingUser::withId(new UserId(self::OBSERVER_ID)));

        $message = sprintf('Вы не следите за днем рождения пользователя с id %s.', self::OBSERVEE_ID);

        $this->translator
            ->method('trans')
            ->with('observee.not_observing', ['%id%' => self::OBSERVEE_ID])
            ->willReturn($message);

        $this->expectMessageToObserver($message);

        $this->command->execute(new UserId(self::OBSERVER_ID), self::VALID_COMMAND);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->observerService = $this->createMock(ObserverService::class);

        $this->command = new StopObserving(
            $this->observerService,
            $this->messenger,
            $this->translator,
        );
    }
}
