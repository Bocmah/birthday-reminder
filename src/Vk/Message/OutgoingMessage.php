<?php

declare(strict_types=1);

namespace Vkbd\Vk\Message;

use Vkbd\Vk\User\Id\NumericVkId;

final class OutgoingMessage
{
    private NumericVkId $to;

    private string $text;

    public function __construct(NumericVkId $to, string $text)
    {
        $this->to = $to;
        $this->text = $text;
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
