<?php

namespace GrinWay\Service\Tests\Unit\Doctrine\Function;

use Doctrine\ORM\EntityManagerInterface;
use GrinWay\Service\Factory\TestFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class AbstractDoctrineFunctionTestCase extends KernelTestCase
{
    use ResetDatabase, Factories;

    protected static EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();

        TestFactory::truncate();
        TestFactory::createOne([
            'text' => 'abs1a2bc09',
            'dateinterval' => new \DateInterval('P12Y34M56DT78H90M09S'),
        ]);

        static::$em = self::getContainer()->get(EntityManagerInterface::class);
    }

}
