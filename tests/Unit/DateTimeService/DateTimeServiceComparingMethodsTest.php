<?php

namespace GrinWay\Service\Tests\Unit\DateTimeService;

use Carbon\Carbon;
use GrinWay\Service\Service\DateTimeService;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversMethod(DateTimeService::class, 'lessThanNow')]
#[CoversMethod(DateTimeService::class, 'notLessThanNow')]
#[CoversMethod(DateTimeService::class, 'greaterThanNow')]
#[CoversMethod(DateTimeService::class, 'notGreaterThanNow')]
class DateTimeServiceComparingMethodsTest extends AbstractDateTimeServiceTestCase
{
    public function testLessThanNow()
    {
        $dateTime = Carbon::now('+12:00')->subSecond(10);
        $this->assertTrue(DateTimeService::lessThanNow($dateTime));

        $dateTime = Carbon::now('+12:00')->addSecond(10);
        $this->assertFalse(DateTimeService::lessThanNow($dateTime));
    }

    public function testNotLessThanNow()
    {
        $dateTime = Carbon::now('+12:00')->addSecond(10);
        $this->assertTrue(DateTimeService::notLessThanNow($dateTime));

        $dateTime = Carbon::now('+12:00')->subSecond(10);
        $this->assertFalse(DateTimeService::notLessThanNow($dateTime));
    }

    public function testGreaterThanNow()
    {
        $dateTime = Carbon::now('+12:00')->addSecond(10);
        $this->assertTrue(DateTimeService::greaterThanNow($dateTime));

        $dateTime = Carbon::now('+12:00')->subSecond(10);
        $this->assertFalse(DateTimeService::greaterThanNow($dateTime));
    }

    public function testNotGreaterThanNow()
    {
        $dateTime = Carbon::now('+12:00')->subSecond(10);
        $this->assertTrue(DateTimeService::notGreaterThanNow($dateTime));

        $dateTime = Carbon::now('+12:00')->addSecond(10);
        $this->assertFalse(DateTimeService::notGreaterThanNow($dateTime));
    }
}
