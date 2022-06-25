<?php

declare(strict_types=1);

namespace BirthdayReminder\Vk\User\Id;

use InvalidArgumentException;
use Stringable;
use Webmozart\Assert\Assert;

final class NumericVkId implements Stringable
{
    private int $id;

    public function __construct(int $id)
    {
        try {
            Assert::greaterThanEq($id, 1);
        } catch (InvalidArgumentException) {
            throw new InvalidNumericVkId('VK id must be greater or equal to 1');
        }

        $this->id = $id;
    }

    public function equals(NumericVkId $other): bool
    {
        return $this->id === $other->id;
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
