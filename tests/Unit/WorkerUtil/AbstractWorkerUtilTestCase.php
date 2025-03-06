<?php

namespace GrinWay\Service\Tests\Unit\WorkerUtil;

use GrinWay\Service\Factory\TestAssociationFactory;
use GrinWay\Service\Factory\TestFactory;
use GrinWay\Service\Service\Messenger\WorkerUtil;
use GrinWay\Service\Test\Service\Messenger\WorkerUtil as TestWorkerUtil;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

#[CoversClass(WorkerUtil::class)]
abstract class AbstractWorkerUtilTestCase extends KernelTestCase
{
    use Factories, ResetDatabase;

    protected static TestWorkerUtil $workerUtil;

    protected function setUp(): void
    {
        parent::setUp();

        TestFactory::truncate();
        TestAssociationFactory::truncate();

        // id 1
        TestFactory::createOne([
            'text' => 'TEST TEXT',
            'test' => 'TEST',
            'association' => TestAssociationFactory::createOne(), // id 1
        ]);
        // id 2
        TestFactory::createOne([
            'association' => null,
            'test' => null,
        ]);

        self::$workerUtil = self::getContainer()->get('.grinway_service.test.worker_util');
    }

}
