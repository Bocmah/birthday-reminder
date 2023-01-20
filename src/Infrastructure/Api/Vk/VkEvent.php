<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Api\Vk;

enum VkEvent: string
{
    case Confirmation = 'confirmation';
}
