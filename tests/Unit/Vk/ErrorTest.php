<?php

declare(strict_types=1);

namespace Tests\Unit\Vk;

use PHPUnit\Framework\TestCase;
use Vkbd\Vk\Error;

final class ErrorTest extends TestCase
{
    public function test_it_can_not_be_created_with_wrong_values(): void
    {
        new Error(Error::INVALID_USER_ID);

        $this->expectExceptionMessage('Unknown error code');

        new Error(24124125);
    }

    public function test_it_compares_itself_to_other_code(): void
    {
        $error = new Error(Error::INVALID_USER_ID);

        self::assertTrue($error->is(Error::INVALID_USER_ID));
        self::assertFalse($error->is(124124512));
    }
}
