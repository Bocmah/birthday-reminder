<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Command;

use BirthdayReminder\Application\Command\Command;
use BirthdayReminder\Application\Command\Exception\ErrorDuringCommandExecution;
use BirthdayReminder\Application\Command\ParseResult;
use BirthdayReminder\Domain\User\UserId;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Translation\TranslatableMessage;
use Tests\Unit\Domain\Observer\ObserverMother;
use Throwable;

/**
 * @covers \BirthdayReminder\Application\Command\Command
 */
final class CommandTest extends TestCase
{
    /**
     * @test
     */
    public function matchesByPattern(): void
    {
        $command = new class() extends Command {
            protected function executedParsed(UserId $observerId, ParseResult $parseResult): string|TranslatableMessage
            {
                return 'done';
            }

            protected function pattern(): string
            {
                return '/add (?<id>\S+)/';
            }
        };

        $this->assertTrue($command->matches('add 123'));

        $this->assertFalse($command->matches('delete 123'));
    }

    /**
     * @test
     */
    public function execute(): void
    {
        $command = new class() extends Command {
            protected function executedParsed(UserId $observerId, ParseResult $parseResult): string|TranslatableMessage
            {
                return 'done';
            }

            protected function pattern(): string
            {
                return '/add (?<id>\S+)/';
            }
        };

        $this->assertEquals(
            'done',
            $command->execute(ObserverMother::createObserverId(), 'add 333')
        );
    }

    /**
     * @test
     */
    public function throwsExceptionIfEncounteredErrorDuringExecution(): void
    {
        $exceptionDuringExecution = new RuntimeException('Something went wrong', 123);

        $command = new class($exceptionDuringExecution) extends Command {
            public function __construct(private readonly Throwable $exception)
            {
            }

            protected function executedParsed(UserId $observerId, ParseResult $parseResult): string|TranslatableMessage
            {
                throw $this->exception;
            }

            protected function pattern(): string
            {
                return '/add (?<id>\S+)/';
            }
        };

        $this->expectExceptionObject(new ErrorDuringCommandExecution('Something went wrong', 123, $exceptionDuringExecution));

        $command->execute(ObserverMother::createObserverId(), 'add 444');
    }
}
