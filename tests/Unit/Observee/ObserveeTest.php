<?php

declare(strict_types=1);

namespace Tests\Unit\Observee;

use PHPUnit\Framework\TestCase;
use BirthdayReminder\Observee\Observee;
use BirthdayReminder\Observer\Domain\Observer;
use BirthdayReminder\Person\FullName;
use BirthdayReminder\Vk\User\Id\NumericVkId;

/**
 * @covers \BirthdayReminder\Observee\Observee
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
