<?php

declare(strict_types=1);

namespace Tests\Unit\Observee;

use BirthdayReminder\Domain\FullName;
use BirthdayReminder\Domain\Observee\Observee;
use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BirthdayReminder\Domain\Observee\Observee
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
            new UserId('123'),
            new FullName('James', 'Dean'),
            new DateTimeImmutable('10.10.1996'),
        );

        $newBirthdate = new DateTimeImmutable('12.10.1999');

        $observee->changeBirthdate($newBirthdate);

        $this->assertEquals($newBirthdate, $observee->birthdate());
    }
}
