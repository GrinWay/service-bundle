<?php

namespace GrinWay\Service\Tests;

use GrinWay\Service\Service\Currency;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Path;

abstract class AbstractTestCase extends WebTestCase
{
    protected Currency $currencyService;
    protected static \Closure $getenv;
    protected static string $appUrl;
    protected static string $kernelCacheDir;

    protected function setUp(): void
    {
        parent::setUp();

        self::$kernelCacheDir = Path::normalize(self::getContainer()->getParameter('kernel.cache_dir'));
        self::$getenv = self::getContainer()->get('container.getenv');
        self::$appUrl = (self::$getenv)('APP_URL');
        $this->currencyService = self::getContainer()->get('grinway_service.currency');
    }
}
