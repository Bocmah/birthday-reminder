<?php

declare(strict_types=1);

namespace Tests\Unit\Vk\User;

use Exception;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use Vkbd\Person\FullName;
use Vkbd\Vk\Api\FailedToCallVkApiMethod;
use Vkbd\Vk\Api\VkApiInterface;
use Vkbd\Vk\User\FailedToRetrieveUser;
use Vkbd\Vk\User\NumericVkId;
use Vkbd\Vk\User\User;
use Vkbd\Vk\User\UserRetriever;

use function Clue\React\Block\await;
use function React\Promise\reject;
use function React\Promise\resolve;

final class UserRetrieverTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_it_retrieves_user_when_api_responds_without_errors(): void
    {
        $id = new NumericVkId(134);
        $name = new FullName('John', 'Doe');
        $expectedUser = new User($id, $name);
        $vkApi = $this->createMock(VkApiInterface::class);

        $vkApi
            ->expects(self::once())
            ->method('callMethod')
            ->with(
                'users.get',
                [
                    'user_ids' => $id->value(),
                ],
            )
            ->willReturn(
                resolve(
                    [
                        'id' => $id->value(),
                        'first_name' => $name->firstName(),
                        'last_name' => $name->lastName(),
                    ],
                ),
            );

        /** @var User $user */
        $user = await(
            (new UserRetriever($vkApi))->retrieve($id),
            Factory::create(),
        );

        self::assertEquals($expectedUser, $user);
    }

    /**
     * @throws Exception
     */
    public function test_it_rejects_on_error(): void
    {
        $vkApi = $this->createMock(VkApiInterface::class);

        $apiError = 'API is on maintenance';
        $vkApi
            ->method('callMethod')
            ->willReturn(
                reject(
                    new FailedToCallVkApiMethod($apiError)
                )
            );

        try {
            await(
                (new UserRetriever($vkApi))->retrieve(new NumericVkId(134)),
                Factory::create(),
            );
        } catch (FailedToRetrieveUser $exception) {
            self::assertEquals(
                "Failed to retrieve user. Reason: $apiError",
                $exception->getMessage()
            );

            return;
        }

        self::fail('Failed to assert that retrieve rejects with ' . FailedToRetrieveUser::class);
    }
}
