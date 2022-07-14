<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Observer;

use BirthdayReminder\Domain\Date\Date;
use BirthdayReminder\Domain\FullName;
use BirthdayReminder\Domain\Observee\Observee;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;

#[Entity]
class Observer
{
    #[Id, Column(type: 'user_id')]
    public readonly UserId $id;

    #[Embedded]
    public readonly FullName $fullName;

    /**
     * @var Collection<int, Observee>
     */
    #[OneToMany(mappedBy: 'observer', targetEntity: Observee::class, cascade: ["persist"], orphanRemoval: true)]
    private Collection $observees;

    #[Column]
    private bool $shouldAlwaysBeNotified = true;

    public function __construct(
        UserId   $id,
        FullName $fullName,
    ) {
        $this->id = $id;
        $this->fullName = $fullName;
        $this->observees = new ArrayCollection();
    }

    public function startObserving(UserId $userId, FullName $fullName, DateTimeImmutable $birthdate): void
    {
        if ($this->observeeExists($userId)) {
            throw AlreadyObservingUser::withId($userId);
        }

        $this->observees->add(new Observee($this, $userId, $fullName, $birthdate));
    }

    public function stopObserving(UserId $userId): void
    {
        $observee = $this->observeeWithId($userId);

        if ($observee === null) {
            throw NotObservingUser::withId($userId);
        }

        $this->observees->removeElement($observee);
    }

    public function changeObserveeBirthdate(UserId $userId, DateTimeImmutable $newBirthdate): void
    {
        $observee = $this->observeeWithId($userId);

        if ($observee === null) {
            throw NotObservingUser::withId($userId);
        }

        $observee->changeBirthdate($newBirthdate);
    }

    /**
     * @return Observee[]
     */
    public function birthdaysOnDate(DateTimeImmutable $date): array
    {
        return array_values(
            $this->observees
                ->filter(fn (Observee $observee): bool => Date::isSameDay($date, $observee->birthdate()))
                ->toArray()
        );
    }

    public function toggleNotifiability(): void
    {
        $this->shouldAlwaysBeNotified = !$this->shouldAlwaysBeNotified;
    }

    /**
     * @return Observee[]
     */
    public function observees(): array
    {
        return array_values($this->observees->toArray());
    }

    public function shouldAlwaysBeNotified(): bool
    {
        return $this->shouldAlwaysBeNotified;
    }

    private function observeeExists(UserId $id): bool
    {
        return $this->observeeWithId($id) !== null;
    }

    private function observeeWithId(UserId $id): ?Observee
    {
        foreach ($this->observees as $observee) {
            if ($observee->userId->equals($id)) {
                return $observee;
            }
        }

        return null;
    }
}
