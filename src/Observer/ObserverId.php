<?php

declare(strict_types=1);

namespace Vkbd\Observer;

use Stringable;
use Webmozart\Assert\Assert;

final class ObserverId implements Stringable
{
    private int $id;

    public function __construct(int $id)
    {
        Assert::greaterThan($id, 0, 'Observer id must be positive');

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
