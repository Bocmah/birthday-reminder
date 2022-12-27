<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Command;

use BirthdayReminder\Application\Command\Describable;
use BirthdayReminder\Application\Command\GetHelp;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tests\Unit\Domain\Observer\ObserverMother;

/**
 * @covers \BirthdayReminder\Application\Command\GetHelp
 */
final class GetHelpTest extends TestCase
{
    private const VALID_COMMAND = 'help';

    /**
     * @test
     */
    public function help(): void
    {
        $describable1 = $this->mockDescribable('name1', 'description1');
        $describable2 = $this->mockDescribable('name2', 'description2');

        $translator = $this->createMock(TranslatorInterface::class);

        $command = new GetHelp([$describable1, $describable2], $translator);

        $expectedMessage = <<<'MESSAGE'
            name1 - description1

            name2 - description2
            MESSAGE;

        $this->assertEquals(
            $expectedMessage,
            $command->execute(ObserverMother::createObserverId(), self::VALID_COMMAND),
        );
    }

    /**
     * @test
     */
    public function emptyMessage(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);

        $command = new GetHelp([], $translator);

        $this->assertEquals(
            new TranslatableMessage('unexpected_error'),
            $command->execute(ObserverMother::createObserverId(), self::VALID_COMMAND),
        );
    }

    /**
     * @return MockObject&Describable
     */
    private function mockDescribable(string $name, string $description): MockObject
    {
        $describable = $this->createMock(Describable::class);

        $describable->method('name')->willReturn($name);

        $message = $this->createMock(TranslatableInterface::class);
        $message->method('trans')->willReturn($description);

        $describable->method('description')->willReturn($message);

        return $describable;
    }
}
