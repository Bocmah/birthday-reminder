<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Observer;

use BirthdayReminder\Domain\Date\Date;
use BirthdayReminder\Domain\FullName;
use BirthdayReminder\Domain\Observee\Observee;
use BirthdayReminder\Domain\Observer\Exception\AlreadyObservingUser;
use BirthdayReminder\Domain\Observer\Exception\NotObservingUser;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbedMany;
use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbedOne;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;

#[Document(db: 'birthday-reminder')]
class Observer
{
    #[Id(type: 'user_id', strategy: 'NONE')]
    public readonly UserId $id;

    #[EmbedOne(targetDocument: FullName::class)]
    public readonly FullName $fullName;

    /**
     * @var Collection<int, Observee>
     */
    #[EmbedMany(targetDocument: Observee::class)]
    private Collection $observees;

    #[Field(type: 'bool')]
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

        $this->observees->add(new Observee($userId, $fullName, $birthdate));
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
