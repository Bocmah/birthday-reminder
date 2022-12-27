<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Api\User;

use BirthdayReminder\Domain\FullName;
use BirthdayReminder\Domain\User\User;
use BirthdayReminder\Domain\User\UserId;
use BirthdayReminder\Infrastructure\Api\User\VkUserFinder;
use BirthdayReminder\Infrastructure\Api\Vk\VkApi;
use BirthdayReminder\Infrastructure\Api\Vk\VkApiMethod;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

/**
 * @covers \BirthdayReminder\Infrastructure\Api\User\VkUserFinder
 */
final class VkUserFinderTest extends TestCase
{
    private const USER_ID = '123';

    /** @var MockObject&VkApi */
    private readonly MockObject $vkApi;

    private readonly VkUserFinder $userFinder;

    /**
     * @test
     */
    public function returnsUserIfHeWasFoundByVkApi(): void
    {
        $this->vkApiWillReturn([['id' => self::USER_ID, 'first_name' => 'John', 'last_name' => 'Doe']]);

        $this->assertEquals(
            new User(new UserId(self::USER_ID), new FullName('John', 'Doe')),
            $this->userFinder->findById(new UserId(self::USER_ID)),
        );
    }

    /**
     * @test
     */
    public function returnsNullIfVkApiReturnedNull(): void
    {
        $this->vkApiWillReturn(null);

        $this->assertNull($this->userFinder->findById(new UserId(self::USER_ID)));
    }

    /**
     * @test
     */
    public function returnsNullIfVkApiReturnedEmptyList(): void
    {
        $this->vkApiWillReturn([]);

        $this->assertNull($this->userFinder->findById(new UserId(self::USER_ID)));
    }

    /**
     * @test
     */
    public function failsIfMoreThanOneUserFoundByVkApi(): void
    {
        $this->vkApiWillReturn([
            ['id' => self::USER_ID, 'first_name' => 'John', 'last_name' => 'Doe'],
            ['id' => self::USER_ID, 'first_name' => 'Joe', 'last_name' => 'James'],
        ]);

        $this->expectExceptionObject(
            new UnexpectedValueException(
                sprintf('Multiple users returned by VK API for id %s', self::USER_ID)
            )
        );

        $this->userFinder->findById(new UserId(self::USER_ID));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->vkApi = $this->createMock(VkApi::class);

        $this->userFinder = new VkUserFinder($this->vkApi);
    }

    /**
     * @param list<array{id: string, first_name: string, last_name: string}>|null $users
     */
    private function vkApiWillReturn(?array $users): void
    {
        $this->vkApi
            ->method('callMethod')
            ->with(VkApiMethod::GetUser, ['user_ids' => self::USER_ID])
            ->willReturn($users);
    }
}
