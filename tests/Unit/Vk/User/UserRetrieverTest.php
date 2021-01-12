<?php

declare(strict_types=1);

namespace Tests\Unit\Vk\User;

use Tests\TestCaseWithPromisesHelpers;
use Vkbd\Person\FullName;
use Vkbd\Vk\Api\FailedToCallVkApiMethod;
use Vkbd\Vk\Api\VkApiInterface;
use Vkbd\Vk\Error;
use Vkbd\Vk\User\FailedToRetrieveUser;
use Vkbd\Vk\User\Id\AlphanumericVkId;
use Vkbd\Vk\User\Id\NumericVkId;
use Vkbd\Vk\User\UnknownError;
use Vkbd\Vk\User\User;
use Vkbd\Vk\User\UserIsDeactivated;
use Vkbd\Vk\User\UserRetriever;
use Vkbd\Vk\User\UserWasNotFound;

use function React\Promise\reject;
use function React\Promise\resolve;

final class UserRetrieverTest extends TestCaseWithPromisesHelpers
{
    public function test_it_retrieves_user_when_api_responds_without_errors(): void
    {
        $id = new NumericVkId(134);
        $name = new FullName('John', 'Doe');
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

        $this->assertResolvesWith(
            (new UserRetriever($vkApi))->retrieve($id),
            new User($id, $name),
        );
    }

    public function test_it_rejects_when_call_method_rejects(): void
    {
        $vkApi = $this->createMock(VkApiInterface::class);
        $vkApi
            ->method('callMethod')
            ->willReturn(
                reject(
                    new FailedToCallVkApiMethod('API is on maintenance'),
                ),
            );

        $this->assertRejectsWith(
            (new UserRetriever($vkApi))->retrieve(new NumericVkId(134)),
            FailedToRetrieveUser::class,
        );
    }

    public function test_it_rejects_when_user_is_deactivated(): void
    {
        $vkApi = $this->createMock(VkApiInterface::class);
        $vkApi
            ->method('callMethod')
            ->willReturn(
                resolve(
                    [
                        'first_name' => 'DELETED',
                        'deactivated' => 'banned',
                    ],
                ),
            );

        $this->assertRejectsWith(
            (new UserRetriever($vkApi))->retrieve(new NumericVkId(134)),
            UserIsDeactivated::class,
        );
    }

    /**
     * @param array $response
     * @param class-string $expectedException
     *
     * @dataProvider errorResponseProvider
     */
    public function test_it_rejects_when_error_is_present_in_response(array $response, string $expectedException): void
    {
        $vkApi = $this->createMock(VkApiInterface::class);
        $vkApi
            ->method('callMethod')
            ->willReturn(
                resolve($response),
            );

        $this->assertRejectsWith(
            (new UserRetriever($vkApi))->retrieve(new AlphanumericVkId('gw3y26523y23gwe')),
            $expectedException,
        );
    }

    /**
     * @return iterable<array{0: array, 1: class-string}>
     */
    public function errorResponseProvider(): iterable
    {
        return [
            [
                [
                    'error' => [
                        'error_code' => Error::INVALID_USER_ID,
                    ],
                ],
                UserWasNotFound::class,
            ],
            [
                [
                    'error' => [
                        'error_code' => 0,
                    ],
                ],
                UnknownError::class,
            ],
            [
                [
                    'error' => [],
                ],
                UnknownError::class,
            ],
        ];
    }
}
