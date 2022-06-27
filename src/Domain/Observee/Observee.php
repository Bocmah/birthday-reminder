<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Observee;

use BirthdayReminder\Domain\FullName;
use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
final class Observee
{
    public function __construct(
        #[Id, ManyToOne(targetEntity: Observer::class, inversedBy: 'observees')]
        private readonly Observer $observer,
        #[Id, Column(type: 'user_id')]
        public readonly UserId    $userId,
        #[Embedded]
        public readonly FullName $fullName,
        #[Column(type: 'date')]
        private DateTimeImmutable $birthdate,
    ) {
    }

    public function changeBirthdate(DateTimeImmutable $newBirthdate): void
    {
        $this->birthdate = $newBirthdate;
    }

    public function birthdate(): DateTimeImmutable
    {
        return $this->birthdate;
    }
}
