<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Command;

use BirthdayReminder\Application\Command\ListObservees;
use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\Date\DateFormatter;
use BirthdayReminder\Domain\FullName;
use BirthdayReminder\Domain\Observee\Observee;
use BirthdayReminder\Domain\Observee\ObserveeFormatter;
use BirthdayReminder\Domain\Observer\ObserverWasNotFoundInTheSystem;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatableMessage;
use Tests\Unit\Domain\Observer\ObserverMother;

/**
 * @covers \BirthdayReminder\Application\Command\ListObservees
 */
final class ListObserveesTest extends TestCase
{
    private const VALID_COMMAND = 'list';

    /** @var MockObject&ObserverService */
    private MockObject $observerService;

    private ListObservees $command;

    /**
     * @test
     */
    public function listObservees(): void
    {
        $observer = ObserverMother::createObserverWithoutObservees();
        $firstObservee = ObserverMother::attachObservee(
            $observer,
            new UserId('444'),
            new FullName('John', 'Doe'),
            new DateTimeImmutable('10.10.1990'),
        );
        $secondObservee = ObserverMother::attachObservee(
            $observer,
            new UserId('555'),
            new FullName('James', 'Dean'),
            new DateTimeImmutable('05.07.1985'),
        );

        $this->observerService
            ->method('getObservees')
            ->with($observer->id)
            ->willReturn(
                [
                    $firstObservee,
                    $secondObservee,
                ],
            );

        $this->assertEquals(
            "*id444 (John Doe) - 10.10.1990\n*id555 (James Dean) - 05.07.1985",
            $this->command->execute($observer->id, self::VALID_COMMAND),
        );
    }

    /**
     * @test
     */
    public function observerWasNotFoundInTheSystem(): void
    {
        $observerId = ObserverMother::createObserverId();

        $this->observerService
            ->method('getObservees')
            ->with($observerId)
            ->willThrowException(ObserverWasNotFoundInTheSystem::withUserId($observerId));

        $this->assertEquals(
            new TranslatableMessage('unexpected_error'),
            $this->command->execute($observerId, self::VALID_COMMAND),
        );
    }

    /**
     * @test
     */
    public function notObservingAnyone(): void
    {
        $observerId = ObserverMother::createObserverId();

        $this->observerService
            ->method('getObservees')
            ->with($observerId)
            ->willReturn([]);

        $this->assertEquals(
            new TranslatableMessage('observee.not_observing_anyone'),
            $this->command->execute($observerId, self::VALID_COMMAND),
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->observerService = $this->createMock(ObserverService::class);

        $dateFormatter = $this->createMock(DateFormatter::class);
        $dateFormatter
            ->method('format')
            ->willReturnCallback(fn (DateTimeImmutable $date) => $date->format('d.m.Y'));

        $observeeFormatter = $this->createMock(ObserveeFormatter::class);
        $observeeFormatter
            ->method('format')
            ->willReturnCallback(fn (Observee $observee) => sprintf(
                '*id%s (%s %s)',
                (string) $observee->userId,
                $observee->fullName->firstName,
                $observee->fullName->lastName,
            ));

        $this->command = new ListObservees($this->observerService, $observeeFormatter, $dateFormatter);
    }
}
