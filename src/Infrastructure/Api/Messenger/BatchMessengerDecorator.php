<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Api\Messenger;

use BirthdayReminder\Domain\Messenger\Messenger;
use BirthdayReminder\Domain\User\UserId;
use Webmozart\Assert\Assert;

final class BatchMessengerDecorator implements Messenger
{
    public function __construct(private readonly Messenger $messenger, private readonly int $batchSize)
    {
        Assert::positiveInteger($this->batchSize, 'Batch size must be positive');
    }

    public function sendMessage(UserId $to, string $text): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        foreach (mb_str_split($text, $this->batchSize) as $batch) {
            $this->messenger->sendMessage($to, $batch);
        }
    }
}
