<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Date;

use BirthdayReminder\Domain\Date\Date;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BirthdayReminder\Domain\Date\Date
 */
final class DateTest extends TestCase
{
    /**
     * @test
     * @dataProvider dates
     */
    public function isSameDay(DateTimeImmutable $dateA, DateTimeImmutable $dateB, bool $result): void
    {
        $this->assertSame($result, Date::isSameDay($dateA, $dateB));
    }

    /**
     * @dataProvider
     *
     * @return iterable<string, array{0: DateTimeImmutable, 1: DateTimeImmutable, 3: bool}
     */
    private function dates(): iterable
    {
        yield 'same day' => [
            new DateTimeImmutable('10.11.1996'),
            new DateTimeImmutable('10.11.2005'),
            true,
        ];

        yield 'not same day' => [
            new DateTimeImmutable('10.11.1996'),
            new DateTimeImmutable('11.11.1996'),
            false,
        ];
    }
}
