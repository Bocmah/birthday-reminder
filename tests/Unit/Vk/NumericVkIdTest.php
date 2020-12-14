<?php

declare(strict_types=1);

namespace Tests\Unit\Vk;

use PHPUnit\Framework\TestCase;
use Vkbd\Vk\NumericVkId;

final class NumericVkIdTest extends TestCase
{
    /**
     * @dataProvider invalidIdsProvider
     *
     * @param int $invalidId
     */
    public function test_you_can_not_create_observer_id_providing_not_positive_number(int $invalidId): void
    {
        $this->expectExceptionMessage('VK id must be greater or equal to 1');

        new NumericVkId($invalidId);
    }

    /**
     * @dataProvider validIdsProvider
     *
     * @param int $validId
     */
    public function test_it_accepts_positive_integers(int $validId): void
    {
        $id = new NumericVkId($validId);

        self::assertSame($validId, $id->id());
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
