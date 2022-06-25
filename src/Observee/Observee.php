<?php

declare(strict_types=1);

namespace BirthdayReminder\Observee;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use BirthdayReminder\Observer\Domain\Observer;
use BirthdayReminder\Person\FullName;
use BirthdayReminder\Platform\PlatformUserId;

#[Entity]
final class Observee
{
    public function __construct(
        #[Id, ManyToOne(targetEntity: Observer::class, inversedBy: 'observees')]
        private readonly Observer $observer,
        #[Id, Column(type: 'platform_user_id')]
        public readonly PlatformUserId $platformUserId,
        #[Embedded]
        private readonly FullName $fullName,
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
