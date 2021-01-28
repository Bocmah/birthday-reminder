<?php

declare(strict_types=1);

namespace Vkbd\Vk\User;

use React\Promise\PromiseInterface;
use Vkbd\Vk\Api\FailedToCallVkApiMethod;
use Vkbd\Vk\Api\VkApiInterface;
use Vkbd\Person\FullName;
use Vkbd\Vk\Error;
use Vkbd\Vk\User\Id\AlphanumericVkId;
use Vkbd\Vk\User\Id\NumericVkId;

class UserRetriever
{
    private VkApiInterface $vkApi;

    public function __construct(VkApiInterface $vkApi)
    {
        $this->vkApi = $vkApi;
    }

    /**
     * @param AlphanumericVkId|NumericVkId $id
     *
     * @return PromiseInterface<User>
     */
    public function retrieve($id): PromiseInterface
    {
        $onFulfilled = function (array $user): User {
            /** @var array{
             *     id: int,
             *     first_name: string,
             *     last_name: string,
             *     deactivated?: string
             * } $user
             */
            $this->checkForErrors($user);
            $this->checkIfUserIsDeactivated($user);

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

    private function checkForErrors(array $response): void
    {
        /** @var array{error_code: int}|null $error */
        $error = $response['error'] ?? null;

        if ($error === null) {
            return;
        }

        if (!isset($error['error_code'])) {
            throw new UnknownError();
        }

        $invalidUserIdError = new Error(Error::INVALID_USER_ID);

        if ($invalidUserIdError->is($error['error_code'])) {
            throw new UserWasNotFound();
        }

        throw new UnknownError();
    }

    private function checkIfUserIsDeactivated(array $response): void
    {
        if (!isset($response['deactivated'])) {
            return;
        }

        if (isset($response['first_name']) && $response['first_name'] === 'DELETED') {
            throw new UserIsDeactivated();
        }
    }
}
