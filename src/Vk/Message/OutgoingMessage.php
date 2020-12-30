<?php

declare(strict_types=1);

namespace Vkbd\Vk\Message;

use Vkbd\Vk\User\VkId;

final class OutgoingMessage
{
    private VkId $to;
    private string $text;

    public function __construct(VkId $to, string $text)
    {
        $this->to = $to;
        $this->text = $text;
    }

    public function to(): VkId
    {
        return $this->to;
    }

    public function text(): string
    {
        return $this->text;
    }
}
