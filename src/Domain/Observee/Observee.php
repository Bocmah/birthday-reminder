<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Observee;

use BirthdayReminder\Domain\FullName;
use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbedOne;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;
use Doctrine\ODM\MongoDB\Mapping\Annotations\ReferenceOne;
use Doctrine\ORM\Mapping\Id;

#[Document]
final class Observee
{
    public function __construct(
        #[Id, ReferenceOne(targetDocument: Observer::class, inversedBy: 'observees')]
        private readonly Observer $observer,
        #[Id, Field(type: 'user_id')]
        public readonly UserId    $userId,
        #[EmbedOne(targetDocument: FullName::class)]
        public readonly FullName $fullName,
        #[Field(type: 'date')]
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
