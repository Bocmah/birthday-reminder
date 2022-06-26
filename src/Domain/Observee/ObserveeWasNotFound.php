<?php

declare(strict_types=1);

namespace BirthdayReminder\Observee;

use RuntimeException;
use BirthdayReminder\Observer\ObserverId;
use BirthdayReminder\Vk\User\Id\NumericVkId;

final class ObserveeWasNotFound extends RuntimeException
{
    public static function withObserverIdAndVkId(ObserverId $observerId, NumericVkId $vkId): self
    {
        return new self(
            "Observee with VK id $vkId belonging to observer with id $observerId was not found"
        );
    }
}
