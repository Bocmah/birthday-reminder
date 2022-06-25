<?php

declare(strict_types=1);

namespace BirthdayReminder\Vk\User\Id;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

final class AlphanumericVkId
{
    private string $id;

    public function __construct(string $id)
    {
        try {
            Assert::alnum($id);
        } catch (InvalidArgumentException) {
            throw new InvalidAlphanumericVkId('VK id must be an alphanumeric string');
        }

        $this->id = $id;
    }

    public function value(): string
    {
        return $this->id;
    }
}
