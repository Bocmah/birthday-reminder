<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Command;

use BirthdayReminder\Application\Command\ErrorDuringCommandExecution;
use BirthdayReminder\Application\Command\StartObserving;
use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\Observee\ObserveeWasNotFoundOnThePlatform;
use BirthdayReminder\Domain\Observer\AlreadyObservingUser;
use BirthdayReminder\Domain\Observer\ObserverWasNotFoundOnThePlatform;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatableMessage;
use Tests\Unit\Domain\Observer\ObserverMother;

/**
 * @covers \BirthdayReminder\Application\Command\StartObserving
 */
final class StartObservingTest extends TestCase
{
    private const BIRTHDATE = '10.05.1990';

    private const VALID_COMMAND = 'add 333 10.05.1990';

    private MockObject|ObserverService $observerService;

    private StartObserving $command;

    /**
     * @test
     */
    public function startObserving(): void
    {
        [$observerId, $observeeId] = ObserverMother::createObserverIdAndObserveeId();

        $this->observerService
            ->expects($this->once())
            ->method('startObserving')
            ->with($observerId, $observeeId, new DateTimeImmutable(self::BIRTHDATE));

        $this->assertEquals(
            new TranslatableMessage('observee.started_observing', ['%id%' => (string) $observeeId]),
            $this->command->execute($observerId, self::VALID_COMMAND),
        );
    }

    /**
     * @test
     */
    public function observeeWasNotFoundOnThePlatform(): void
    {
        [$observerId, $observeeId] = ObserverMother::createObserverIdAndObserveeId();

        $this->observerService
            ->method('startObserving')
            ->with($observerId, $observeeId, new DateTimeImmutable(self::BIRTHDATE))
            ->willThrowException(ObserveeWasNotFoundOnThePlatform::withUserId($observeeId));

        $this->assertEquals(
            new TranslatableMessage('user.not_found_on_the_platform', ['%id%' => (string) $observeeId]),
            $this->command->execute($observerId, self::VALID_COMMAND),
        );
    }

    /**
     * @test
     */
    public function alreadyObserving(): void
    {
        [$observerId, $observeeId] = ObserverMother::createObserverIdAndObserveeId();

        $this->observerService
            ->method('startObserving')
            ->with($observerId, $observeeId, new DateTimeImmutable(self::BIRTHDATE))
            ->willThrowException(AlreadyObservingUser::withId($observeeId));

        $this->assertEquals(
            new TranslatableMessage('observee.already_observing', ['%id%' => (string) $observeeId]),
            $this->command->execute($observerId, self::VALID_COMMAND),
        );
    }

    /**
     * @test
     */
    public function observerWasNotFoundOnThePlatform(): void
    {
        [$observerId, $observeeId] = ObserverMother::createObserverIdAndObserveeId();

        $this->observerService
            ->method('startObserving')
            ->with($observerId, $observeeId, new DateTimeImmutable(self::BIRTHDATE))
            ->willThrowException(ObserverWasNotFoundOnThePlatform::withUserId($observerId));

        $this->assertEquals(
            new TranslatableMessage('unexpected_error'),
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

        $this->command = new StartObserving($this->observerService);
    }
}
