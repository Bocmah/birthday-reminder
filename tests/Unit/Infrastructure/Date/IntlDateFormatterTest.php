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
        yield 'today should be displayed as a word' => [new DateTimeImmutable('today', new DateTimeZone('Europe/Moscow')), 'сегодня'];

        yield 'tomorrow should be displayed as a word' => [new DateTimeImmutable('tomorrow', new DateTimeZone('Europe/Moscow')), 'завтра'];

        yield 'other dates should be displayed as day and month' => [new DateTimeImmutable('16.10.1996', new DateTimeZone('Europe/Moscow')), '16 октября'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatter = new IntlDateFormatter('ru_RU', new DateTimeZone('Europe/Moscow'), new SystemCalendar());
    }
}
