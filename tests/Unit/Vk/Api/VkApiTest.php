<?php

declare(strict_types=1);

namespace Tests\Unit\Vk\Api;

use Exception;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use React\Http\Browser;
use React\Http\Message\Response;
use Vkbd\Vk\Api\Config;
use Vkbd\Vk\Api\FailedToCallVkApiMethod;
use Vkbd\Vk\Api\VkApi;

use function Clue\React\Block\await;
use function React\Promise\reject;
use function React\Promise\resolve;

final class VkApiTest extends TestCase
{
    private const TEST_METHOD = 'messages.send';

    /**
     * @throws Exception
     */
    public function test_it_calls_api_and_decodes_response(): void
    {
        $config = $this->createConfig();
        $parameters = $this->createParameters();

        $response = [
            'response' => 9497009,
        ];

        $browser = $this->createMock(Browser::class);

        $browser
            ->expects(self::once())
            ->method('get')
            ->with(
                "{$config->baseUrl()}/method/".self::TEST_METHOD."?access_token={$config->token()}&v={$config->version()}&user_id={$parameters['user_id']}&message={$parameters['message']}"
            )
            ->willReturn(
                resolve(
                    new Response(
                        200,
                        [],
                        json_encode($response, JSON_THROW_ON_ERROR),
                    )
                )
            );

        $result = await(
            (new VkApi($config, $browser))->callMethod(self::TEST_METHOD, $parameters),
            Factory::create(),
        );

        self::assertEquals($response, $result);
    }

    /**
     * @throws Exception
     */
    public function test_it_rejects_on_unexpected_status_code(): void
    {
        $config = $this->createConfig();

        $responseCode = 500;
        $browser = $this->createMock(Browser::class);
        $browser
            ->method('get')
            ->willReturn(
                resolve(
                    new Response(
                        $responseCode,
                        [],
                        json_encode('Server error', JSON_THROW_ON_ERROR)
                    )
                )
            );

        try {
            await(
                (new VkApi($config, $browser))
                    ->callMethod(self::TEST_METHOD, $this->createParameters()),
                Factory::create()
            );
        } catch (FailedToCallVkApiMethod $exception) {
            self::assertEquals(
                "Failed to call VK api. Received unexpected status code $responseCode",
                $exception->getMessage(),
            );
            return;
        }

        self::fail('Failed to assert that callMethod rejects with ' . FailedToCallVkApiMethod::class);
    }

    /**
     * @throws Exception
     */
    public function test_it_rejects_on_error(): void
    {
        $config = $this->createConfig();

        $browser = $this->createMock(Browser::class);
        $error = 'Network error';

        $browser
            ->method('get')
            ->willReturn(reject(new Exception($error)));

        try {
            await(
                (new VkApi($config, $browser))
                    ->callMethod(self::TEST_METHOD, $this->createParameters()),
                Factory::create()
            );
        } catch (FailedToCallVkApiMethod $exception) {
            self::assertEquals(
                'Failed to call method '.self::TEST_METHOD.". Reason: $error",
                $exception->getMessage(),
            );
            return;
        }

        self::fail('Failed to assert that callMethod rejects with ' . FailedToCallVkApiMethod::class);
    }

    private function createConfig(): Config
    {
        return new Config('https://api.vk.com', 'ge131gger23', '5.11');
    }

    /**
     * @return array{
     *      user_id: int,
     *      message: string
     * }
     */
    private function createParameters(): array
    {
        return [
            'user_id' => 790,
            'message' => 'FooBar',
        ];
    }
}
