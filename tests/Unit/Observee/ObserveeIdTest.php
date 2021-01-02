<?php

declare(strict_types=1);

namespace Tests\Unit\Observee;

use PHPUnit\Framework\TestCase;
use Vkbd\Observee\ObserveeId;

final class ObserveeIdTest extends TestCase
{
    /**
     * @dataProvider invalidIdsProvider
     *
     * @param int $invalidId
     */
    public function test_it_can_not_be_created_with_not_positive_number(int $invalidId): void
    {
        $this->expectExceptionMessage('Observee id must be positive');

        new ObserveeId($invalidId);
    }

    /**
     * @dataProvider validIdsProvider
     *
     * @param int $validId
     */
    public function test_it_accepts_positive_integers(int $validId): void
    {
        $id = new ObserveeId($validId);

        self::assertSame($validId, $id->value());
    }

    /**
     * @return iterable<int[]>
     */
    public function invalidIdsProvider(): iterable
    {
        return [
            [-1],
            [0],
        ];
    }

    /**
     * @return iterable<int[]>
     */
    public function validIdsProvider(): iterable
    {
        return [
            [1],
            [2],
            [1345],
        ];
    }
}
