<?php

declare(strict_types=1);

namespace BirthdayReminder\Vk\Message;

use BirthdayReminder\Vk\User\Id\NumericVkId;

final class OutgoingMessage
{
    public function __construct(
        private NumericVkId $to,
        private string $text
    ) {
    }

    public function to(): NumericVkId
    {
        return $this->to;
    }

    public function text(): string
    {
        return $this->text;
    }
}
