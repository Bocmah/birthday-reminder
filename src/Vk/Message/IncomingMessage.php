<?php

declare(strict_types=1);

namespace BirthdayReminder\Vk\Message;

use BirthdayReminder\Vk\User\Id\NumericVkId;

final class IncomingMessage
{
    public function __construct(
        private NumericVkId $from,
        private string $text
    ) {
    }

    public function from(): NumericVkId
    {
        return $this->from;
    }

    public function text(): string
    {
        return $this->text;
    }
}
