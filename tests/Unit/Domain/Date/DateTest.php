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
     * @see isSameDay()
     *
     * @return iterable<string, array{dateA: DateTimeImmutable, dateB: DateTimeImmutable, result: bool}
     */
    public function dates(): iterable
    {
        yield 'same day' => [
            'dateA'  => new DateTimeImmutable('10.11.1996'),
            'dateB'  => new DateTimeImmutable('10.11.2005'),
            'result' => true,
        ];

        yield 'not same day' => [
            'dateA'  => new DateTimeImmutable('10.11.1996'),
            'dateB'  => new DateTimeImmutable('11.11.1996'),
            'result' => false,
        ];
    }
}
