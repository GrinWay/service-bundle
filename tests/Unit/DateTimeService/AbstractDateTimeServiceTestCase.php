<?php

namespace GrinWay\Service\Tests\Unit\DateTimeService;

use Carbon\CarbonInterval;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractDateTimeServiceTestCase extends KernelTestCase
{
    protected static CarbonInterval $interval1Y;
    protected static CarbonInterval $interval2Y;

    protected function setUp(): void
    {
        parent::setUp();

        static::$interval1Y = CarbonInterval::createFromFormat('Y-m-d H:i:s', '01-00-00 00:00:00');
        static::$interval2Y = CarbonInterval::createFromFormat('Y-m-d H:i:s', '02-00-00 00:00:00');
    }
}
