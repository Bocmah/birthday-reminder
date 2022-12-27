<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Observee;

use BirthdayReminder\Domain\FullName;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;
use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbeddedDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbedOne;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;

#[EmbeddedDocument]
class Observee
{
    public function __construct(
        #[Field(type: 'user_id')]
        public readonly UserId $userId,
        #[EmbedOne(targetDocument: FullName::class)]
        public readonly FullName $fullName,
        #[Field(type: 'date_immutable')]
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
