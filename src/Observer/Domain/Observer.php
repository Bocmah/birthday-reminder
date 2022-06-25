<?php

declare(strict_types=1);

namespace BirthdayReminder\Observer\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use BirthdayReminder\Observee\Observee;
use BirthdayReminder\Person\FullName;
use BirthdayReminder\Platform\PlatformUserId;

#[Entity]
class Observer
{
    #[Id, Column(type: 'platform_user_id')]
    private readonly PlatformUserId $id;

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
        PlatformUserId $id,
        FullName $fullName,
    ) {
        $this->id = $id;
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

    public function startObserving(PlatformUserId $userId, FullName $fullName, \DateTimeImmutable $birthdate): void
    {
        $this->observees->add(new Observee($this, $userId, $fullName, $birthdate));
    }

    public function stopObserving(PlatformUserId $userId): void
    {
        foreach ($this->observees as $key => $observee) {
            if ($observee->platformUserId->equals($userId)) {
                $this->observees->remove($key);
                return;
            }
        }

        throw NotObservingUser::withId($userId);
    }

    public function changeObserveeBirthdate(PlatformUserId $userId, \DateTimeImmutable $newBirthdate): void
    {
        foreach ($this->observees as $observee) {
            if ($observee->platformUserId->equals($userId)) {
                $observee->changeBirthdate($newBirthdate);
                return;
            }
        }

        throw NotObservingUser::withId($userId);
    }

    public function toggleNotifiability(): void
    {
        $this->shouldAlwaysBeNotified = !$this->shouldAlwaysBeNotified;
    }
}
