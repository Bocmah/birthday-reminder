<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Command;

use BirthdayReminder\Application\Command\ChangeBirthdate;
use BirthdayReminder\Application\Command\ErrorDuringCommandExecution;
use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\Observer\NotObservingUser;
use BirthdayReminder\Domain\Observer\ObserverWasNotFoundInTheSystem;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatableMessage;
use Tests\Unit\Domain\Observer\ObserverMother;

/**
 * @covers \BirthdayReminder\Application\Command\ChangeBirthdate
 */
final class ChangeBirthdateTest extends TestCase
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
        [$observerId, $observeeId] = ObserverMother::createObserverIdAndObserveeId();

        $this->observerService
            ->expects($this->once())
            ->method('changeObserveeBirthdate')
            ->with($observerId, $observeeId, new DateTimeImmutable(self::BIRTHDATE));

        $this->assertEquals(
            new TranslatableMessage('observee.birthday_changed', ['%id%' => (string) $observeeId]),
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
            ->method('changeObserveeBirthdate')
            ->with($observerId, $observeeId, new DateTimeImmutable(self::BIRTHDATE))
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
            ->method('changeObserveeBirthdate')
            ->with($observerId, $observeeId, new DateTimeImmutable(self::BIRTHDATE))
            ->willThrowException(NotObservingUser::withId($observerId));

        $this->assertEquals(
            new TranslatableMessage('observee.not_observing', ['%id%' => (string) $observeeId]),
            $this->command->execute($observerId, self::VALID_COMMAND),
        );
    }

    /**
     * @test
     *
     * @dataProvider invalidCommandFormatProvider
     */
    public function invalidCommandFormat(string $command): void
    {
        $this->expectException(ErrorDuringCommandExecution::class);

        $this->command->execute(ObserverMother::createObserverId(), $command);
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

        $this->command = new ChangeBirthdate($this->observerService);
    }
}
