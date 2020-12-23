<?php

declare(strict_types=1);

namespace Vkbd\Observer;

use RuntimeException;
use Vkbd\Vk\NumericVkId;

final class ObserverWasNotFound extends RuntimeException
{
    public static function withVkId(NumericVkId $vkId): self
    {
        return new self("Observer with VK id $vkId was not found");
    }
}
