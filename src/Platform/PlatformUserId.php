<?php

declare(strict_types=1);

namespace BirthdayReminder\Platform;

use Stringable;
use Webmozart\Assert\Assert;

final class PlatformUserId implements Stringable
{
    private readonly string $id;

    public function __construct(string $id)
    {
        Assert::minLength($id, 1, 'Platform user id should not be blank');

        $this->id = $id;
    }

    public function equals(PlatformUserId $other): bool
    {
        return $this->id === $other->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
