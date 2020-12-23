<?php

declare(strict_types=1);

namespace Vkbd\Person;

use Webmozart\Assert\Assert;

final class FullName
{
    private string $firstName;
    private string $lastName;

    public function __construct(string $firstName, string $lastName)
    {
        Assert::alpha($firstName, 'First name can only contain letters');
        Assert::alpha($lastName, 'Last name can only contain letters');

        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }
}
