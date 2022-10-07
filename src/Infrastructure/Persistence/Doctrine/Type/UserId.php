<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\StringType;

final class UserId extends StringType
{
    private const NAME = 'user_id';

    public function convertToPHPValue($value, AbstractPlatform $platform): \BirthdayReminder\Domain\User\UserId
    {
        /** @var int|string $value */
        $value = parent::convertToPHPValue($value, $platform);

        return new \BirthdayReminder\Domain\User\UserId((string) $value);
    }

    /**
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return parent::convertToDatabaseValue((string) $value, $platform);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
