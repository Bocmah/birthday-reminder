<?php

declare(strict_types=1);

namespace Tests\Unit\Vk\Api;

use Exception;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use React\Http\Browser;
use Vkbd\Vk\Api\Config;
use Vkbd\Vk\Api\FailedToCallVkApiMethod;
use Vkbd\Vk\Api\VkApi;

use function Clue\React\Block\await;
use function React\Promise\reject;
use function React\Promise\resolve;

final class VkApiTest extends TestCase
{
    public function test_it_calls_api_method_with_right_parameters(): void
    {
        $config = new Config('https://api.vk.com', 'ge131gger23', '5.11');
        $parameters = [
            'user_id' => 790,
            'message' => 'FooBar',
        ];
        $method = 'messages.send';

        $browser = $this->createMock(Browser::class);

        $browser
            ->expects(self::once())
            ->method('get')
            ->with(
                "{$config->baseUrl()}/method/$method?access_token={$config->token()}&v={$config->version()}&user_id={$parameters['user_id']}&message={$parameters['message']}"
            )
            ->willReturn(resolve([]));

        (new VkApi($config, $browser))->callMethod($method, $parameters);
    }

    /**
     * @throws Exception
     */
    public function test_it_rejects_on_error(): void
    {
        $config = new Config('https://api.vk.com', 'ge131gger23', '5.11');
        $parameters = [
            'user_id' => 790,
            'message' => 'FooBar',
        ];
        $method = 'messages.send';

        $browser = $this->createMock(Browser::class);
        $error = 'Network error';

        $browser
            ->method('get')
            ->willReturn(reject(new Exception($error)));

        $api = new VkApi($config, $browser);

        try {
            await(
                $api->callMethod($method, $parameters),
                Factory::create()
            );
        } catch (FailedToCallVkApiMethod $exception) {
            self::assertEquals(
                "Failed to call method $method. Reason: $error",
                $exception->getMessage()
            );
            return;
        }

        self::fail('Failed to assert that callMethod rejects with ' . FailedToCallVkApiMethod::class);
    }
}
