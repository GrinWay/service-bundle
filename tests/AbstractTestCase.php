<?php

namespace GrinWay\Service\Tests;

use GrinWay\Service\Service\Currency;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractTestCase extends WebTestCase
{
    protected Currency $currencyService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currencyService = self::getContainer()->get('grinway_service.currency');
    }
}
