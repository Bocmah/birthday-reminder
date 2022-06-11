<?php

declare(strict_types=1);

namespace Tests\Unit\Observee;

use PHPUnit\Framework\TestCase;
use Vkbd\Observee\Observee;
use Vkbd\Observer\Domain\Observer;
use Vkbd\Person\FullName;
use Vkbd\Vk\User\Id\NumericVkId;

/**
 * @covers \Vkbd\Observee\Observee
 */
final class ObserveeTest extends TestCase
{
    /**
     * @test
     */
    public function changeBirthdate(): void
    {
        $observee = new Observee(
            $this->createMock(Observer::class),
            new NumericVkId(123),
            new FullName('James', 'Dean'),
            new \DateTimeImmutable('10.10.1996'),
        );

        $newBirthdate = new \DateTimeImmutable('12.10.1999');

        $observee->changeBirthdate($newBirthdate);

        $this->assertEquals($newBirthdate, $observee->birthdate());
    }
}
