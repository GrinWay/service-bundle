<?php

namespace GrinWay\Service\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use GrinWay\Service\Doctrine\Type\Percent;

/**
 * Percent DBAL type
 *
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
class PercentType extends Type
{
    public const NAME = 'percent';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'DECIMAL(5, 2)';
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        return new Percent($value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        \assert($value instanceof Percent);
        return $value->toFloat();
    }
}
