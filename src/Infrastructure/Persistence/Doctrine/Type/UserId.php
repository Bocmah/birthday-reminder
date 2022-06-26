<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\StringType;

final class PlatformUserId extends StringType
{
    private const NAME = 'platform_user_id';

    public function convertToPHPValue($value, AbstractPlatform $platform): \BirthdayReminder\Domain\Platform\UserId
    {
        $value = parent::convertToPHPValue($value, $platform);

        return new \BirthdayReminder\Domain\Platform\UserId($value);
    }

    /**
     * @param \BirthdayReminder\Domain\Platform\UserId $value
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): int
    {
        return parent::convertToDatabaseValue((string) $value, $platform);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
