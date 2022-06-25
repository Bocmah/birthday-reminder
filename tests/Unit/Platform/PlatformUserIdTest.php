<?php

declare(strict_types=1);

namespace Tests\Unit\Platform;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use BirthdayReminder\Platform\PlatformUserId;

/**
 * @covers \BirthdayReminder\Platform\PlatformUserId
 */
final class PlatformUserIdTest extends TestCase
{
    /**
     * @test
     * @dataProvider successfulCreationData
     */
    public function successfulCreation(string $id): void
    {
        $platformUserId = new PlatformUserId($id);

        $this->assertSame($id, (string) $platformUserId);
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    private function successfulCreationData(): iterable
    {
        yield 'lowercase string' => ['lowercasestring'];

        yield 'uppercase string' => ['UPPERCASESTRING'];

        yield 'mixed case string' => ['MiXeDcAsEsTrInG'];

        yield 'numbers string' => ['123'];

        yield 'string with spaces' => ['string with spaces'];

        yield 'string with special symbols' => ['string_with-special.symbols'];
    }

    /**
     * @test
     * @dataProvider unsuccessfulCreationData
     */
    public function unsuccessfulCreation(string $id): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PlatformUserId($id);
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    private function unsuccessfulCreationData(): iterable
    {
        yield 'blank string' => [''];
    }
}
