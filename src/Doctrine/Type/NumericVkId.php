<?php

declare(strict_types=1);

namespace Vkbd\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\IntegerType;

final class NumericVkId extends IntegerType
{
    private const NAME = 'vk_id';

    public function convertToPHPValue($value, AbstractPlatform $platform): \Vkbd\Vk\User\Id\NumericVkId
    {
        $value = parent::convertToPHPValue($value, $platform);

        return new \Vkbd\Vk\User\Id\NumericVkId($value);
    }

    /**
     * @param \Vkbd\Vk\User\Id\NumericVkId $value
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): int
    {
        return parent::convertToDatabaseValue($value->value(), $platform);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
