<?php

declare(strict_types=1);

namespace Tests\Unit\Vk\Api\Message;

use Exception;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use Vkbd\Vk\Api\FailedToCallVkApiMethod;
use Vkbd\Vk\Api\VkApiInterface;
use Vkbd\Vk\Message\FailedToSendMessage;
use Vkbd\Vk\Message\Messenger;
use Vkbd\Vk\Message\OutgoingMessage;
use Vkbd\Vk\User\NumericVkId;

use function Clue\React\Block\await;
use function React\Promise\reject;
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
                    'user_id' => $message->to()->value(),
                    'message' => $message->text(),
                ]
            )
            ->willReturn(resolve([]));

        (new Messenger($vkApi))->send($message);
    }

    /**
     * @throws Exception
     */
    public function test_it_rejects_on_error(): void
    {
        $message = new OutgoingMessage(new NumericVkId(123), 'FooBar');
        $vkApi = $this->createMock(VkApiInterface::class);

        $apiError = 'API is on maintenance';
        $vkApi
            ->method('callMethod')
            ->willReturn(
                reject(
                    new FailedToCallVkApiMethod($apiError)
                )
            );

        $messenger = new Messenger($vkApi);

        try {
            await(
                $messenger->send($message),
                Factory::create()
            );
        } catch (FailedToSendMessage $exception) {
            self::assertEquals(
                "Failed to send message. Reason: $apiError",
                $exception->getMessage()
            );

            return;
        }

        self::fail('Failed to assert that send rejects with ' . FailedToSendMessage::class);
    }
}
