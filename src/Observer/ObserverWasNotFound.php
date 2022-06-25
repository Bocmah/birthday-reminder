<?php

declare(strict_types=1);

namespace BirthdayReminder\Observer;

use RuntimeException;
use BirthdayReminder\Vk\User\Id\NumericVkId;

final class ObserverWasNotFound extends RuntimeException
{
    public static function withVkId(NumericVkId $vkId): self
    {
        return new self("Observer with VK id $vkId was not found");
    }
}
