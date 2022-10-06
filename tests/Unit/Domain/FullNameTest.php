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


    /**
     * @see canNotBeCreatedWithInvalidFirstName()
     * @see canNotBeCreatedWithInvalidLastName()
     *
     * @return iterable<string, array{0: string}>
     */
    public function invalidNames(): iterable
    {
        yield 'name containing only numbers' => ['12345'];

        yield 'blank name' => [''];

        yield 'name containing numbers and letters' => ['John15'];

        yield 'name containing punctuation' => [','];
    }
}
