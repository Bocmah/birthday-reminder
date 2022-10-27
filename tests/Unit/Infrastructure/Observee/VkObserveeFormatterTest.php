<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Observee;

use BirthdayReminder\Domain\FullName;
use BirthdayReminder\Domain\User\UserId;
use BirthdayReminder\Infrastructure\Observee\VkObserveeFormatter;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Domain\Observer\ObserverMother;

final class VkObserveeFormatterTest extends TestCase
{
    /**
     * @test
     */
    public function formatsObservee(): void
    {
        $observee = ObserverMother::attachObservee(
            ObserverMother::createObserverWithoutObservees(),
            new UserId('1890'),
            new FullName('John', 'Doe'),
        );

        $this->assertEquals(
            '*id1890 (John Doe)',
            (new VkObserveeFormatter())->format($observee),
        );
    }
}
