<?php

declare(strict_types=1);

namespace Tests\Unit\Person;

use PHPUnit\Framework\TestCase;
use Vkbd\Person\FullName;

final class FullNameTest extends TestCase
{
    /**
     * @dataProvider invalidNames
     *
     * @param string $name
     */
    public function test_it_can_not_be_created_with_invalid_first_name(string $name): void
    {
        $this->expectExceptionMessage('First name can only contain letters');

        new FullName($name, 'Doe');
    }

    /**
     * @dataProvider invalidNames
     *
     * @param string $name
     */
    public function test_it_can_not_be_created_with_invalid_last_name(string $name): void
    {
        $this->expectExceptionMessage('Last name can only contain letters');

        new FullName('John', $name);
    }

    /**
     * @return iterable<string[]>
     */
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
