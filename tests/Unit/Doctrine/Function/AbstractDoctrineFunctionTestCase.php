<?php

namespace GrinWay\Service\Tests\Unit\Doctrine\Function;

use Carbon\CarbonInterval;
use Doctrine\ORM\EntityManagerInterface;
use GrinWay\Service\Factory\TestFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class AbstractDoctrineFunctionTestCase extends KernelTestCase
{
    use ResetDatabase, Factories;

    protected static EntityManagerInterface $em;
    protected static ?\DateInterval $positiveDateInterval = null;
    protected static \DateInterval $negativeDateInterval;

    protected function setUp(): void
    {
        parent::setUp();

        if (null === static::$positiveDateInterval) {
            static::$positiveDateInterval = CarbonInterval::createFromFormat('Y-m-d H:i:s', '12-34-56 78:90:09');
        }

        static::$negativeDateInterval = CarbonInterval::instance(static::$positiveDateInterval);
        static::$negativeDateInterval->invert();

        TestFactory::truncate();
        TestFactory::createOne([
            'text' => 'abs1a2bc09',
            'dateinterval' => static::$positiveDateInterval,
        ]);
        TestFactory::createOne([
            'text' => 'positive',
            'dateinterval' => static::$positiveDateInterval,
        ]);
        TestFactory::createOne([
            'text' => 'negative',
            'dateinterval' => static::$negativeDateInterval,
        ]);

        static::$em = self::getContainer()->get(EntityManagerInterface::class);
    }

}
