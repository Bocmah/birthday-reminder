<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Command;

use BirthdayReminder\Application\Command\ChangeBirthdate;
use BirthdayReminder\Application\Command\InvalidCommandFormat;
use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\Messenger\Messenger;
use BirthdayReminder\Domain\Observer\NotObservingUser;
use BirthdayReminder\Domain\Observer\ObserverWasNotFoundInTheSystem;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @covers \BirthdayReminder\Application\Command\ChangeBirthdate
 */
final class ChangeBirthdateTest extends TestCase
{
    private const OBSERVER_ID = '123';

    private const OBSERVEE_ID = '333';

    private const BIRTHDATE = '10.05.1990';

    private const VALID_COMMAND = 'update 333 10.05.1990';

    private MockObject|ObserverService $observerService;

    private MockObject|Messenger $messenger;

    private MockObject|TranslatorInterface $translator;

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

        $this->expectMessage($message);

        $this->command->execute(new UserId(self::OBSERVER_ID), self::VALID_COMMAND);
    }

    /**
     * @test
     */
    public function informsIssuerAboutNotObservingObserveeWhenObserverWasNotFoundInTheSystem(): void
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

        $this->expectMessage($message);

        $this->command->execute(new UserId(self::OBSERVER_ID), self::VALID_COMMAND);
    }

    /**
     * @test
     */
    public function informsIssuerWhenNotObservingUser(): void
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
        $this->messenger = $this->createMock(Messenger::class);
        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->command = new ChangeBirthdate(
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
