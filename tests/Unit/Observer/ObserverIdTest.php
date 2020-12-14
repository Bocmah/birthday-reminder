<?php

declare(strict_types=1);

namespace Tests\Unit\Observer;

use PHPUnit\Framework\TestCase;
use Vkbd\Observer\ObserverId;

final class ObserverIdTest extends TestCase
{
    public function test_you_can_not_create_observer_id_providing_not_positive_number(): void
    {
        $this->expectExceptionMessage('Observer id must be positive');

        new ObserverId(-1);
    }
}
