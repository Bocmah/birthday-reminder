<?php

declare(strict_types=1);

namespace Vkbd\Vk\User;

use Webmozart\Assert\Assert;

final class NumericVkId
{
    private int $id;

    public function __construct(int $id)
    {
        Assert::greaterThanEq($id, 1, 'VK id must be greater or equal to 1');

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
