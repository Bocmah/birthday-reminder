<?php

declare(strict_types=1);

namespace Vkbd\Vk\Message;

use React\Promise\PromiseInterface;
use Vkbd\Vk\Api\VkApiInterface;

final class Messenger
{
    private VkApiInterface $vkApi;

    public function __construct(VkApiInterface $vkApi)
    {
        $this->vkApi = $vkApi;
    }

    public function send(OutgoingMessage $message): PromiseInterface
    {
        return $this->vkApi->callMethod('messages.send', [
            'user_id' => $message->to()->id(),
            'message' => $message->text(),
        ]);
    }
}
