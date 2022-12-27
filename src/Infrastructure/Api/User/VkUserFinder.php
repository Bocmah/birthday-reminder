<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Api\User;

use BirthdayReminder\Domain\FullName;
use BirthdayReminder\Domain\User\User;
use BirthdayReminder\Domain\User\UserFinder;
use BirthdayReminder\Domain\User\UserId;
use BirthdayReminder\Infrastructure\Api\Vk\VkApi;
use BirthdayReminder\Infrastructure\Api\Vk\VkApiMethod;
use UnexpectedValueException;

final class VkUserFinder implements UserFinder
{
    public function __construct(private readonly VkApi $vkApi)
    {
    }

    public function findById(UserId $id): ?User
    {
        /** @var list<array{id: int, first_name: string, last_name: string}>|null $users */
        $users = $this->vkApi->callMethod(VkApiMethod::GetUser, ['user_ids' => (string) $id]);

        if (!\is_array($users) || $users === []) {
            return null;
        }

        $this->ensureOnlyOneUserForId($users, $id);

        $user = $users[0];

        return new User(
            new UserId((string) $user['id']),
            new FullName($user['first_name'], $user['last_name']),
        );
    }

    private function ensureOnlyOneUserForId(array $users, UserId $id): void
    {
        if (\count($users) > 1) {
            throw new UnexpectedValueException(
                sprintf('Multiple users returned by VK API for id %s', (string) $id),
            );
        }
    }
}
