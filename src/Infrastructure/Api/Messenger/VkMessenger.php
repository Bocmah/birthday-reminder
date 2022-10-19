<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Api\Messenger;

use BirthdayReminder\Domain\Messenger\Messenger;
use BirthdayReminder\Domain\User\UserId;
use BirthdayReminder\Infrastructure\Api\Vk\VkApi;
use BirthdayReminder\Infrastructure\Api\Vk\VkApiMethod;

final class VkMessenger implements Messenger
{
    public function __construct(private readonly VkApi $vkApi)
    {
    }

    public function sendMessage(UserId $to, string $text): void
    {
        $this->vkApi->callMethod(
            VkApiMethod::SendMessage,
            [
                'user_id' => (string) $to,
                'message' => $text,
            ],
        );
    }
}
