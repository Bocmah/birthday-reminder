<?php

declare(strict_types=1);

namespace Vkbd\Observee;

use Webmozart\Assert\Assert;

final class ObserveeId
{
    private int $id;

    public function __construct(int $id)
    {
        Assert::greaterThan($id, 0, 'Observee id must be positive');

        $this->id = $id;
    }

    public function id(): int
    {
        return $this->id;
    }
}
