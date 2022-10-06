<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Date;

use BirthdayReminder\Infrastructure\Date\IntlDateFormatter;
use BirthdayReminder\Infrastructure\Date\SystemCalendar;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use PHPUnit\Framework\TestCase;

final class IntlDateFormatterTest extends TestCase
{
    private readonly DateTimeZone $timezone;

    private readonly IntlDateFormatter $formatter;

    /**
     * @test
     * @dataProvider dates
     */
    public function format(DateTimeImmutable $date, string $result): void
    {
        $this->assertSame($result, $this->formatter->format($date));
    }

    /**
     * @see format()
     *
     * @return iterable<string, array{0: DateTimeImmutable, 1: string}>
     *
     * @throws Exception
     */
    public function dates(): iterable
    {
        yield 'today should be displayed as a word' => [new DateTimeImmutable('today', $this->timezone), 'сегодня'];

        yield 'tomorrow should be displayed as a word' => [new DateTimeImmutable('tomorrow', $this->timezone), 'завтра'];

        yield 'other dates should be displayed as day and month' => [new DateTimeImmutable('16.10.1996', $this->timezone), '16 октября'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->timezone = new DateTimeZone('Europe/Moscow');
        $this->formatter = new IntlDateFormatter('ru_RU', $this->timezone, new SystemCalendar());
    }
}
