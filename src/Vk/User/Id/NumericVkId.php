<?php

declare(strict_types=1);

namespace Vkbd\Vk\User\Id;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

final class NumericVkId
{
    private int $id;

    /** @noinspection BadExceptionsProcessingInspection */
    public function __construct(int $id)
    {
        try {
            Assert::greaterThanEq($id, 1);
        } catch (InvalidArgumentException $exception) {
            throw new InvalidNumericVkId('VK id must be greater or equal to 1');
        }

        $this->id = $id;
    }

    public function value(): int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }
}
