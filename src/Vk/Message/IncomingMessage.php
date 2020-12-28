<?php

declare(strict_types=1);

namespace Vkbd\Vk\Message;

use Vkbd\Vk\User\NumericVkId;

final class IncomingMessage
{
    private NumericVkId $from;
    private string $text;

    public function __construct(NumericVkId $from, string $text)
    {
        $this->from = $from;
        $this->text = $text;
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
