<?php

namespace GrinWay\Service\Doctrine\DBAL\Type;

use Carbon\WeekDay;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Carbon\WeekDay DBAL type
 *
 * #[ORM\Column(type: WeekDayType::NAME)]
 * private ?\Carbon\WeekDay $weekDay = null;
 *
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
class WeekDayType extends Type
{
    const NAME = 'week_day';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'varchar(1)';
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        return WeekDay::from((int)$value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        \assert($value instanceof WeekDay);
        return $value->value;
    }
}
