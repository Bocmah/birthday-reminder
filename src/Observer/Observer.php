<?php

declare(strict_types=1);

namespace Vkbd\Observer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Vkbd\Observee\Observee;
use Vkbd\Person\FullName;
use Vkbd\Vk\User\Id\NumericVkId;

#[Entity]
class Observer
{
    #[Id, Column(type: 'vk_id')]
    private readonly NumericVkId $vkId;

    #[Embedded]
    private readonly FullName $fullName;

    /**
     * @var Collection<int, Observee>
     */
    #[OneToMany(mappedBy: 'observer', targetEntity: Observee::class, cascade: ["persist"], orphanRemoval: true)]
    private Collection $observees;

    #[Column]
    private bool $shouldAlwaysBeNotified = true;

    public function __construct(
        NumericVkId $vkId,
        FullName $fullName,
    ) {
        $this->vkId = $vkId;
        $this->fullName = $fullName;
        $this->observees = new ArrayCollection();
    }

    /**
     * @return Observee[]
     */
    public function observees(): array
    {
        return $this->observees->toArray();
    }

    public function shouldAlwaysBeNotified(): bool
    {
        return $this->shouldAlwaysBeNotified;
    }

    public function startObserving(NumericVkId $vkId, FullName $fullName, \DateTimeImmutable $birthdate): void
    {
        $this->observees->add(new Observee($this, $vkId, $fullName, $birthdate));
    }

    public function stopObserving(NumericVkId $vkId): void
    {
        foreach ($this->observees as $key => $observee) {
            if ($observee->vkId->equals($vkId)) {
                $this->observees->remove($key);
                return;
            }
        }

        throw NotObservingUser::withId($vkId);
    }

    public function changeObserveeBirthdate(NumericVkId $vkId, \DateTimeImmutable $newBirthdate): void
    {
        foreach ($this->observees as $observee) {
            if ($observee->vkId->equals($vkId)) {
                $observee->changeBirthdate($newBirthdate);
                return;
            }
        }

        throw NotObservingUser::withId($vkId);
    }

    public function toggleNotifiability(): void
    {
        $this->shouldAlwaysBeNotified = !$this->shouldAlwaysBeNotified;
    }
}
