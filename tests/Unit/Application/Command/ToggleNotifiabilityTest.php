<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Command;

use BirthdayReminder\Application\Command\ToggleNotifiability;
use BirthdayReminder\Application\ObserverService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatableMessage;
use Tests\Unit\Domain\Observer\ObserverMother;

/**
 * @covers \BirthdayReminder\Application\Command\ToggleNotifiability
 */
final class ToggleNotifiabilityTest extends TestCase
{
    private const VALID_COMMAND = 'notify';

    /** @var MockObject&ObserverService */
    private MockObject $observerService;

    private ToggleNotifiability $command;

    /**
     * @test
     */
    public function toggleNotifiability(): void
    {
        $observer = ObserverMother::createObserverWithoutObservees();

        $this->observerService
            ->method('toggleNotifiability')
            ->with($observer->id)
            ->willReturnCallback(static fn () => $observer->toggleNotifiability());

        $this->observerService
            ->method('getObserverById')
            ->with($observer->id)
            ->willReturn($observer);

        $this->assertEquals(
            new TranslatableMessage('notify_only_on_upcoming_birthdays'),
            $this->command->execute($observer->id, self::VALID_COMMAND),
        );

        $this->assertEquals(
            new TranslatableMessage('always_notify'),
            $this->command->execute($observer->id, self::VALID_COMMAND),
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->observerService = $this->createMock(ObserverService::class);

        $this->command = new ToggleNotifiability($this->observerService);
    }
}
