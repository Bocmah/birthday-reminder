<?php

declare(strict_types=1);

namespace Tests\Unit\Vk;

use PHPUnit\Framework\TestCase;
use Vkbd\Vk\Event;

final class EventTest extends TestCase
{
    public function test_it_can_not_be_created_with_wrong_values(): void
    {
        new Event(Event::CONFIRMATION);
        new Event(Event::NEW_MESSAGE);

        $this->expectExceptionMessage('Unknown event type');

        new Event('foo');
    }
}
