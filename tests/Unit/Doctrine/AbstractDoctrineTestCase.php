<?php

namespace GrinWay\Service\Tests\Unit\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use GrinWay\Service\Test\Entity\Test;
use GrinWay\Service\Tests\Unit\AbstractUnitTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class AbstractDoctrineTestCase extends AbstractUnitTestCase
{
    use Factories, ResetDatabase;

    protected static EntityManagerInterface $em;

    public const FULL_DATE_TIME_FORMAT = 'Y-m-d H:i:s e';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$em = self::getContainer()->get(EntityManagerInterface::class);
    }

    /**
     * Helper
     */
    protected static function removeAboveSeconds(\DateTimeInterface $dateTime): \DateTimeInterface
    {
        return $dateTime->setTime(
            (int)$dateTime->format('G'), // hours
            (int)$dateTime->format('i'), // minutes
            (int)$dateTime->format('s')  // seconds
        );
    }

    /**
     * Helper
     */
    protected function getTestEntity(): Test
    {
        $testEntity = new Test();
        $testEntity->setText('text');
        $testEntity->setDateTime(new \DateTimeImmutable());
        return $testEntity;
    }
}
