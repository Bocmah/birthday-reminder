<?php

declare(strict_types=1);

namespace Tests\Unit\Vk\Api\Message;

use PHPUnit\Framework\TestCase;
use Vkbd\Vk\Api\VkApiInterface;
use Vkbd\Vk\Message\Messenger;
use Vkbd\Vk\Message\OutgoingMessage;
use Vkbd\Vk\User\NumericVkId;

use function React\Promise\resolve;

final class MessengerTest extends TestCase
{
    public function test_it_calls_right_api_method_with_right_parameters(): void
    {
        $message = new OutgoingMessage(new NumericVkId(123), 'FooBar');
        $vkApi = $this->createMock(VkApiInterface::class);

        $vkApi
            ->expects(self::once())
            ->method('callMethod')
            ->with(
                'messages.send',
                [
                    'user_id' => $message->to()->id(),
                    'message' => $message->text(),
                ]
            )
            ->willReturn(resolve([]));

        (new Messenger($vkApi))->send($message);
    }
}
