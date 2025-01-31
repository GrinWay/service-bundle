<?php

namespace GrinWay\Service\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractUnitTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        //
        self::bootKernel();
        //
        self::ensureKernelShutdown();
    }


}
