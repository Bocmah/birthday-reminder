<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain;

use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbeddedDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;
use Webmozart\Assert\Assert;

#[EmbeddedDocument]
final class FullName
{
    #[Field(type: 'string')]
    public readonly string $firstName;

    #[Field(type: 'string')]
    public readonly string $lastName;

    public function __construct(string $firstName, string $lastName)
    {
        Assert::alpha($firstName, 'First name can only contain letters');
        Assert::alpha($lastName, 'Last name can only contain letters');

        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
}
