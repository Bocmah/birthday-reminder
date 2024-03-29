<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\ODM\MongoDB\Types\ClosureToPHP;
use Doctrine\ODM\MongoDB\Types\StringType;

final class UserId extends StringType
{
    use ClosureToPHP;

    private const NAME = 'user_id';

    public function convertToDatabaseValue($value): mixed
    {
        return parent::convertToDatabaseValue((string) $value);
    }

    public function convertToPHPValue($value): \BirthdayReminder\Domain\User\UserId
    {
        return new \BirthdayReminder\Domain\User\UserId((string) $value);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
