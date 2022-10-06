<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Command;

use BirthdayReminder\Application\Command\Command;
use BirthdayReminder\Application\Command\CommandSelector;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BirthdayReminder\Application\Command\CommandSelector
 */
final class CommandSelectorTest extends TestCase
{
    /**
     * @test
     */
    public function selectsCommandWhichMatchesText(): void
    {
        $text = 'add 123 13.10.1990';

        $command1 = $this->createMock(Command::class);
        $command2 = $this->createMock(Command::class);

        $command1
            ->method('matches')
            ->with($text)
            ->willReturn(false);
        $command2
            ->method('matches')
            ->with($text)
            ->willReturn(true);

        $selector = new CommandSelector([$command1, $command2]);

        $this->assertEquals($command2, $selector->select($text));
    }

    /**
     * @test
     */
    public function failsWhenCommandWasNotFound(): void
    {
        $text = 'agmwplgklpg';

        $command1 = $this->createMock(Command::class);

        $command1
            ->method('matches')
            ->with($text)
            ->willReturn(false);

        $selector = new CommandSelector([$command1]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('No command was found for "agmwplgklpg"');

        $selector->select($text);
    }
}
