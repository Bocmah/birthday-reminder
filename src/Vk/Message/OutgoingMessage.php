<?php

declare(strict_types=1);

namespace Vkbd\Vk\Message;

use Vkbd\Vk\User\Id\NumericVkId;

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
