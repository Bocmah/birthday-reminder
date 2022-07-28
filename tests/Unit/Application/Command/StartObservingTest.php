<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Command;

use BirthdayReminder\Application\Command\InvalidCommandFormat;
use BirthdayReminder\Application\Command\StartObserving;
use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\Messenger\Messenger;
use BirthdayReminder\Domain\Observee\ObserveeWasNotFoundOnThePlatform;
use BirthdayReminder\Domain\Observer\AlreadyObservingUser;
use BirthdayReminder\Domain\Observer\ObserverWasNotFoundOnThePlatform;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @covers \BirthdayReminder\Application\Command\StartObserving
 */
final class StartObservingTest extends TestCase
{
    private const OBSERVER_ID = '123';

    private const OBSERVEE_ID = '333';

    private const BIRTHDATE = '10.05.1990';

    private const VALID_COMMAND = 'add 333 10.05.1990';

    private MockObject|ObserverService $observerService;

    private MockObject|Messenger $messenger;

    private MockObject|TranslatorInterface $translator;

    private StartObserving $command;

    /**
     * @test
     */
    public function startObserving(): void
    {
        $this->observerService
            ->expects($this->once())
            ->method('startObserving')
            ->with(new UserId(self::OBSERVER_ID), new UserId(self::OBSERVEE_ID), new DateTimeImmutable(self::BIRTHDATE));

        $message = sprintf('Теперь вы следите за днем рождения пользователя с id %s.', self::OBSERVEE_ID);

        $this->translator
            ->method('trans')
            ->with('observee.started_observing', ['%id%' => self::OBSERVEE_ID])
            ->willReturn($message);

        $this->expectMessage($message);

        $this->command->execute(new UserId(self::OBSERVER_ID), self::VALID_COMMAND);
    }

    /**
     * @test
     */
    public function informsIssuerWhenObserveeWasNotFoundOnThePlatform(): void
    {
        $this->observerService
            ->method('startObserving')
            ->with(new UserId(self::OBSERVER_ID), new UserId(self::OBSERVEE_ID), new DateTimeImmutable(self::BIRTHDATE))
            ->willThrowException(ObserveeWasNotFoundOnThePlatform::withUserId(new UserId(self::OBSERVEE_ID)));

        $message = sprintf('Пользователь с id %s не найден.', self::OBSERVEE_ID);

        $this->translator
            ->method('trans')
            ->with('user.not_found_on_the_platform', ['%id%' => self::OBSERVEE_ID])
            ->willReturn($message);

        $this->expectMessage($message);

        $this->command->execute(new UserId(self::OBSERVER_ID), self::VALID_COMMAND);
    }

    /**
     * @test
     */
    public function informsIssuerWhenAlreadyObservingUser(): void
    {
        $this->observerService
            ->method('startObserving')
            ->with(new UserId(self::OBSERVER_ID), new UserId(self::OBSERVEE_ID), new DateTimeImmutable(self::BIRTHDATE))
            ->willThrowException(AlreadyObservingUser::withId(new UserId(self::OBSERVEE_ID)));

        $message = sprintf('Вы уже следите за днем рождения пользователя с id %s.', self::OBSERVEE_ID);

        $this->translator
            ->method('trans')
            ->with('observee.already_observing', ['%id%' => self::OBSERVEE_ID])
            ->willReturn($message);

        $this->expectMessage($message);

        $this->command->execute(new UserId(self::OBSERVER_ID), self::VALID_COMMAND);
    }

    /**
     * @test
     */
    public function informsUserAboutUnexpectedErrorWhenObserverWasNotFoundOnThePlatform(): void
    {
        $this->observerService
            ->method('startObserving')
            ->with(new UserId(self::OBSERVER_ID), new UserId(self::OBSERVEE_ID), new DateTimeImmutable(self::BIRTHDATE))
            ->willThrowException(ObserverWasNotFoundOnThePlatform::withUserId(new UserId(self::OBSERVER_ID)));

        $message = 'Произошла непредвиденная ошибка.';

        $this->translator
            ->method('trans')
            ->with('unexpected_error')
            ->willReturn($message);

        $this->expectMessage($message);

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
        yield 'uppercase' => ['ADD 333 10.05.1990'];

        yield 'mixed case' => ['aDd 333 10.05.1990'];

        yield 'spaces in id' => ['add 3 33 10.05.1990'];

        yield 'invalid date' => ['add 333 10/05/1990'];

        yield 'no id' => ['add 10.05.1990'];

        yield 'no date' => ['add 333'];

        yield 'unrelated command' => ['list'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->observerService = $this->createMock(ObserverService::class);
        $this->messenger = $this->createMock(Messenger::class);
        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->command = new StartObserving(
            $this->observerService,
            $this->messenger,
            $this->translator,
        );
    }

    private function expectMessage(string $message): void
    {
        $this->messenger
            ->expects($this->once())
            ->method('sendMessage')
            ->with(new UserId(self::OBSERVER_ID), $message);
    }
}
