<?php

declare(strict_types=1);

namespace Vkbd\Person;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use Webmozart\Assert\Assert;

#[Embeddable]
final class FullName
{
    #[Column]
    private readonly string $firstName;

    #[Column]
    private readonly string $lastName;

    public function __construct(string $firstName, string $lastName)
    {
        Assert::alpha($firstName, 'First name can only contain letters');
        Assert::alpha($lastName, 'Last name can only contain letters');

        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
}
