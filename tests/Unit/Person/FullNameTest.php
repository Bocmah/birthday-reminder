<?php

declare(strict_types=1);

namespace Tests\Unit\Person;

use PHPUnit\Framework\TestCase;
use Vkbd\Person\FullName;

final class FullNameTest extends TestCase
{
    public function test_you_can_not_create_full_name_providing_empty_first_name(): void
    {
        $this->expectExceptionMessage('First name can not be empty');

        new FullName('', 'Doe');
    }

    public function test_you_can_not_create_full_name_providing_empty_last_name(): void
    {
        $this->expectExceptionMessage('Last name can not be empty');

        new FullName('John', '');
    }
}
