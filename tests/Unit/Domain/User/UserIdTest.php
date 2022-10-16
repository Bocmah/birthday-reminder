<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\User;

use BirthdayReminder\Domain\User\UserId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BirthdayReminder\Domain\User\UserId
 */
final class UserIdTest extends TestCase
{
    /**
     * @test
     * @dataProvider successfulCreationProvider
     */
    public function successfulCreation(string $id): void
    {
        $platformUserId = new UserId($id);

        $this->assertSame($id, (string) $platformUserId);
    }

    /**
     * @see successfulCreation()
     *
     * @return iterable<string, array{0: string}>
     */
    public function successfulCreationProvider(): iterable
    {
        yield 'lowercase string' => ['lowercasestring'];

        yield 'uppercase string' => ['UPPERCASESTRING'];

        yield 'mixed case string' => ['MiXeDcAsEsTrInG'];

        yield 'numbers string' => ['123'];

        yield 'string with spaces' => ['string with spaces'];

        yield 'string with special symbols' => ['string_with-special.symbols'];

        yield 'id with only one character' => ['1'];
    }

    /**
     * @test
     * @dataProvider unsuccessfulCreationProvider
     */
    public function unsuccessfulCreation(string $id): void
    {
        $this->expectException(InvalidArgumentException::class);

        new UserId($id);
    }

    /**
     * @see unsuccessfulCreation()
     *
     * @return iterable<string, array{0: string}>
     */
    public function unsuccessfulCreationProvider(): iterable
    {
        yield 'blank string' => [''];
    }
}
