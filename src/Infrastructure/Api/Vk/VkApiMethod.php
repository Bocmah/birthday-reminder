<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Api\Vk;

enum VkApiMethod: string
{
    case GetUser = 'users.get';
    case SendMessage = 'messages.send';
}
