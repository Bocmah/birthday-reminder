<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Command;

use BirthdayReminder\Domain\Messenger\Messenger;
use BirthdayReminder\Domain\User\UserId;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class CommandTestCase extends TestCase
{
    protected const OBSERVER_ID = '123';

    protected const OBSERVEE_ID = '333';

    protected MockObject|Messenger $messenger;

    protected MockObject|TranslatorInterface $translator;

    protected function expectMessageToObserver(string $message): void
    {
        $this->messenger
            ->expects($this->once())
            ->method('sendMessage')
            ->with(new UserId(self::OBSERVER_ID), $message);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->messenger = $this->createMock(Messenger::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
    }
}
