<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Command;

use BirthdayReminder\Application\Command\ChangeBirthdate;
use BirthdayReminder\Application\Command\InvalidCommandFormat;
use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\Observer\NotObservingUser;
use BirthdayReminder\Domain\Observer\ObserverWasNotFoundInTheSystem;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \BirthdayReminder\Application\Command\ChangeBirthdate
 */
final class ChangeBirthdateTest extends CommandTestCase
{
    private const BIRTHDATE = '10.05.1990';

    private const VALID_COMMAND = 'update 333 10.05.1990';

    private MockObject|ObserverService $observerService;

    private ChangeBirthdate $command;

    /**
     * @test
     */
    public function changeBirthdate(): void
    {
        $this->observerService
            ->expects($this->once())
            ->method('changeObserveeBirthdate')
            ->with(new UserId(self::OBSERVER_ID), new UserId(self::OBSERVEE_ID), new DateTimeImmutable(self::BIRTHDATE));

        $message = sprintf('День рождения пользователя с id %s был измененен.', self::OBSERVEE_ID);

        $this->translator
            ->method('trans')
            ->with('observee.birthday_changed', ['%id%' => self::OBSERVEE_ID])
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
            ->method('changeObserveeBirthdate')
            ->with(new UserId(self::OBSERVER_ID), new UserId(self::OBSERVEE_ID), new DateTimeImmutable(self::BIRTHDATE))
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
            ->method('changeObserveeBirthdate')
            ->with(new UserId(self::OBSERVER_ID), new UserId(self::OBSERVEE_ID), new DateTimeImmutable(self::BIRTHDATE))
            ->willThrowException(NotObservingUser::withId(new UserId(self::OBSERVER_ID)));

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
     *
     * @dataProvider invalidCommandFormatProvider
     */
    public function invalidCommandFormat(string $command): void
    {
        $this->expectException(InvalidCommandFormat::class);

        $this->command->execute(new UserId(self::OBSERVER_ID), $command);
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    public function invalidCommandFormatProvider(): iterable
    {
        yield 'uppercase' => ['UPDATE 333 10.05.1990'];

        yield 'mixed case' => ['uPdAtE 333 10.05.1990'];

        yield 'spaces in id' => ['update 3 33 10.05.1990'];

        yield 'invalid date' => ['update 333 10/05/1990'];

        yield 'no id' => ['update 10.05.1990'];

        yield 'no date' => ['update 333'];

        yield 'unrelated command' => ['add 333 10.05.1990'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->observerService = $this->createMock(ObserverService::class);

        $this->command = new ChangeBirthdate(
            $this->observerService,
            $this->messenger,
            $this->translator,
        );
    }
}
