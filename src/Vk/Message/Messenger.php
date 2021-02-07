<?php

declare(strict_types=1);

namespace Vkbd\Vk\Message;

use React\Promise\PromiseInterface;
use Vkbd\Vk\Api\FailedToCallVkApiMethod;
use Vkbd\Vk\Api\VkApiInterface;

final class Messenger
{
    public function __construct(private VkApiInterface $vkApi)
    {
    }

    public function send(OutgoingMessage $message): PromiseInterface
    {
        return $this->vkApi
            ->callMethod('messages.send', [
            'user_id' => $message->to()->value(),
            'message' => $message->text(),
        ])
            ->then(
                null,
                static function (FailedToCallVkApiMethod $exception): void {
                    throw FailedToSendMessage::because($exception->getMessage());
                }
            );
    }
}
