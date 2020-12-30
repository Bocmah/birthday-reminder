<?php

declare(strict_types=1);

namespace Vkbd\Vk\Message;

use Vkbd\Vk\User\AlphanumericVkId;
use Vkbd\Vk\User\NumericVkId;

final class OutgoingMessage
{
    /** @var AlphanumericVkId|NumericVkId */
    private $to;
    private string $text;

    /**
     * @param AlphanumericVkId|NumericVkId $to
     * @param string $text
     */
    public function __construct($to, string $text)
    {
        $this->to = $to;
        $this->text = $text;
    }

    /**
     * @return AlphanumericVkId|NumericVkId
     */
    public function to()
    {
        return $this->to;
    }

    public function text(): string
    {
        return $this->text;
    }
}
