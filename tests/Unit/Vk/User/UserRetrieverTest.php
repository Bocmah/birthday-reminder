<?php

declare(strict_types=1);

namespace Tests\Unit\Vk\User;

use Exception;
use Tests\TestCaseWithPromisesHelpers;
use Vkbd\Person\FullName;
use Vkbd\Vk\Api\FailedToCallVkApiMethod;
use Vkbd\Vk\Api\VkApiInterface;
use Vkbd\Vk\User\FailedToRetrieveUser;
use Vkbd\Vk\User\NumericVkId;
use Vkbd\Vk\User\User;
use Vkbd\Vk\User\UserRetriever;

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

    /**
     * @throws Exception
     */
    public function test_it_rejects_on_error(): void
    {
        $vkApi = $this->createMock(VkApiInterface::class);
        $vkApi
            ->method('callMethod')
            ->willReturn(
                reject(
                    new FailedToCallVkApiMethod('API is on maintenance')
                )
            );

        $this->assertRejectsWith(
            (new UserRetriever($vkApi))->retrieve(new NumericVkId(134)),
            FailedToRetrieveUser::class,
        );
    }
}
