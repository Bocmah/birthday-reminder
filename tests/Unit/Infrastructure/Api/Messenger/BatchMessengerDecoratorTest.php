<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Api\Messenger;

use BirthdayReminder\Domain\Messenger\Messenger;
use BirthdayReminder\Domain\User\UserId;
use BirthdayReminder\Infrastructure\Api\Messenger\BatchMessengerDecorator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BirthdayReminder\Infrastructure\Api\Messenger\BatchMessengerDecorator
 */
final class BatchMessengerDecoratorTest extends TestCase
{
    /**
     * @test
     */
    public function sendsMessagesAsBatches(): void
    {
        $userId = new UserId('100');

        $messenger = $this->createMock(Messenger::class);
        $decorator = new BatchMessengerDecorator($messenger, 7);

        $messenger
            ->expects($this->exactly(3))
            ->method('sendMessage')
            ->withConsecutive(
                [$userId, 'Привет,'],
                [$userId, ' userna'],
                [$userId, 'me!'],
            );

        $decorator->sendMessage($userId, 'Привет, username!');
    }

    /**
     * @test
     *
     * @dataProvider notPositiveBatchSizes
     */
    public function batchSizeMustBePositive(int $batchSize): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Batch size must be positive');

        new BatchMessengerDecorator($this->createMock(Messenger::class), $batchSize);
    }

    /**
     * @return iterable<string, array{0: int}>
     */
    public function notPositiveBatchSizes(): iterable
    {
        yield 'negative' => [-100];

        yield 'zero' => [0];
    }
}
