<?php

declare(strict_types=1);

namespace Vkbd\Observee;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Vkbd\Observer\Observer;
use Vkbd\Person\FullName;
use Vkbd\Vk\User\Id\NumericVkId;

#[Entity]
final class Observee
{
    public function __construct(
        #[Id, ManyToOne(targetEntity: Observer::class, inversedBy: 'observees')]
        private readonly Observer $observer,
        #[Id, Column(type: 'vk_id')]
        public readonly NumericVkId $vkId,
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
