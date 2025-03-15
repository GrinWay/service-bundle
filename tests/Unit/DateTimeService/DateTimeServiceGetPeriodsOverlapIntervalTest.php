<?php

namespace GrinWay\Service\Tests\Unit\DateTimeService;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use GrinWay\Service\Service\DateTimeService;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversMethod(DateTimeService::class, 'getPeriodsOverlapInterval')]
class DateTimeServiceGetPeriodsOverlapIntervalTest extends AbstractDateTimeServiceTestCase
{
    public function testTwoSamePeriods()
    {
        $dateTime11 = Carbon::createFromFormat('Y-m-d H:i:s', '2000-01-01 12:34:56');
        $dateTime12 = Carbon::createFromFormat('Y-m-d H:i:s', '2000-01-02 12:34:56');
        $period1 = CarbonPeriod::between($dateTime11, $dateTime12);

        $dateTime21 = Carbon::createFromFormat('Y-m-d H:i:s', '2000-01-01 12:34:56');
        $dateTime22 = Carbon::createFromFormat('Y-m-d H:i:s', '2000-01-02 12:34:56');
        $period2 = CarbonPeriod::between($dateTime21, $dateTime22);

        $overlapInterval = DateTimeService::getPeriodsOverlapInterval(
            period1: $period1,
            period2: $period2,
        );

        $this->assertSame(1, $overlapInterval->day);
    }

    public function test11HoursOverlap()
    {
        $dateTime11 = Carbon::createFromFormat('Y-m-d H:i:s', '2000-01-01 12:34:56');
        $dateTime12 = Carbon::createFromFormat('Y-m-d H:i:s', '2000-01-02 12:34:56');
        $period1 = CarbonPeriod::between($dateTime11, $dateTime12);

        $dateTime21 = Carbon::createFromFormat('Y-m-d H:i:s', '2000-01-02 01:34:56');
        $dateTime22 = Carbon::createFromFormat('Y-m-d H:i:s', '2000-01-03 12:34:56');
        $period2 = CarbonPeriod::between($dateTime21, $dateTime22);

        $overlapInterval = DateTimeService::getPeriodsOverlapInterval(
            period1: $period1,
            period2: $period2,
        );

        $this->assertSame(11, $overlapInterval->hour);
    }

    public function testComplexOverlap()
    {
        $dateTime11 = Carbon::createFromFormat('Y-m-d H:i:s', '2000-01-01 12:34:56');
        $dateTime12 = Carbon::createFromFormat('Y-m-d H:i:s', '2000-01-02 12:34:56');
        $period1 = CarbonPeriod::between($dateTime11, $dateTime12);

        $dateTime21 = Carbon::createFromFormat('Y-m-d H:i:s', '2000-01-02 01:00:00');
        $dateTime22 = Carbon::createFromFormat('Y-m-d H:i:s', '2000-01-03 12:34:56');
        $period2 = CarbonPeriod::between($dateTime21, $dateTime22);

        $overlapInterval = DateTimeService::getPeriodsOverlapInterval(
            period1: $period1,
            period2: $period2,
        );

        $this->assertSame(11, $overlapInterval->hour);
        $this->assertSame(34, $overlapInterval->minute);
        $this->assertSame(56, $overlapInterval->second);
    }
}
