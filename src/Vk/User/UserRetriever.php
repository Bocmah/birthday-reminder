<?php

declare(strict_types=1);

namespace Vkbd\Vk\User;

use React\Promise\PromiseInterface;
use Vkbd\Vk\Api\FailedToCallVkApiMethod;
use Vkbd\Vk\Api\VkApiInterface;
use Vkbd\Person\FullName;

final class UserRetriever
{
    private VkApiInterface $vkApi;

    public function __construct(VkApiInterface $vkApi)
    {
        $this->vkApi = $vkApi;
    }

    /**
     * @param AlphanumericVkId|NumericVkId $id
     *
     * @return PromiseInterface
     */
    public function retrieve($id): PromiseInterface
    {
        $onFulfilled = static function (array $user): User {
            /** @var array{id: int, first_name: string, last_name: string} $user */

            return new User(
                new NumericVkId($user['id']),
                new FullName($user['first_name'], $user['last_name'])
            );
        };
        $onRejected = static function (FailedToCallVkApiMethod $exception): void {
            throw FailedToRetrieveUser::because($exception->getMessage());
        };

        return $this->vkApi
            ->callMethod(
                'users.get',
                [
                    'user_ids' => $id->value(),
                ]
            )
            ->then($onFulfilled, $onRejected);
    }
}
