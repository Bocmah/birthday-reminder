<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use BirthdayReminder\Domain\FullName;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BirthdayReminder\Domain\FullName
 */
final class FullNameTest extends TestCase
{
    /**
     * @test
     * @dataProvider invalidNames
     */
    public function canNotBeCreatedWithInvalidFirstName(string $name): void
    {
        $this->expectExceptionMessage('First name can only contain letters');

        new FullName($name, 'Doe');
    }

    /**
     * @test
     * @dataProvider invalidNames
     */
    public function canNotBeCreatedWithInvalidLastName(string $name): void
    {
        $this->expectExceptionMessage('Last name can only contain letters');

        new FullName('John', $name);
    }

    public function invalidNames(): iterable
    {
        return [
            ['12345'],
            [''],
            ['John15'],
            [','],
        ];
    }
}
