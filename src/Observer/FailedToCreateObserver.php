<?php

declare(strict_types=1);

namespace Vkbd\Observer;

use RuntimeException;

final class FailedToCreateObserver extends RuntimeException
{
    public static function because(string $reason): self
    {
        return new self("Failed top create observer. Reason: $reason");
    }
}
