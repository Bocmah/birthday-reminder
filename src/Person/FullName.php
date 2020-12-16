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
        Assert::stringNotEmpty($firstName, 'First name can not be empty');
        Assert::stringNotEmpty($lastName, 'Last name can not be empty');

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
