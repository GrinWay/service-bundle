<?php

namespace GrinWay\Service\Tests\Unit\DateTimeService;

use Carbon\CarbonInterval;
use GrinWay\Service\Service\DateTimeService;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversMethod(DateTimeService::class, 'getIntervals')]
class DateTimeServiceGetIntervalsTest extends AbstractDateTimeServiceTestCase
{
    public function testThrowsExceptionOnInvalidIterateByString()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid CarbonInterval string, look: \'https://carbon.nesbot.com/docs/\'');
        DateTimeService::getIntervals(
            minInterval: CarbonInterval::instance(static::$interval1Y)->addDays(1),
            maxInterval: static::$interval2Y,
            iterateByValueUnits: 'months',
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid CarbonInterval string, look: \'https://carbon.nesbot.com/docs/\'');
        DateTimeService::getIntervals(
            minInterval: CarbonInterval::instance(static::$interval1Y)->addDays(1),
            maxInterval: static::$interval2Y,
            iterateByValueUnits: '3',
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid CarbonInterval string, look: \'https://carbon.nesbot.com/docs/\'');
        DateTimeService::getIntervals(
            minInterval: CarbonInterval::instance(static::$interval1Y)->addDays(1),
            maxInterval: static::$interval2Y,
            iterateByValueUnits: '3 months!',
        );
    }

    public function testMinMaxIntervalByDefault()
    {
        $intervals = DateTimeService::getIntervals(
            minInterval: static::$interval1Y,
            maxInterval: static::$interval2Y,
        );

        $this->assertCount(2, $intervals);
        $this->assertEquals((string)$intervals[0], (string)static::$interval1Y);
        $this->assertEquals((string)$intervals[1], (string)static::$interval2Y);
    }

    public function test3Intervals()
    {
        $intervals = DateTimeService::getIntervals(
            minInterval: static::$interval1Y,
            maxInterval: static::$interval2Y,
            iterateByCount: 3,
        );

        $this->assertCount(3, $intervals);
        $this->assertEquals((string)$intervals[0], (string)static::$interval1Y);
        $this->assertEquals(
            (string)$intervals[1],
            (string)CarbonInterval::year()->addMonths(4)->cascade(),
        );
        $this->assertEquals((string)$intervals[2], (string)static::$interval2Y);
    }

    public function test3IntervalsWithFilterToLeave1Interval()
    {
        $intervals = DateTimeService::getIntervals(
            minInterval: static::$interval1Y,
            maxInterval: static::$interval2Y,
            iterateByCount: 3,
            filterInterval: static fn(CarbonInterval $i) => '' . $i === '' . static::$interval1Y,
        );

        $this->assertCount(1, $intervals);
        $this->assertEquals((string)$intervals[0], (string)static::$interval1Y);
    }

    public function testIterateBy3Months()
    {
        $intervals = DateTimeService::getIntervals(
            minInterval: static::$interval1Y,
            maxInterval: static::$interval2Y,
            iterateByValueUnits: '3 months',
        );

        $this->assertCount(5, $intervals);
        $this->assertEquals((string)$intervals[0], (string)static::$interval1Y);
        $this->assertEquals(
            (string)CarbonInterval::years(1)->add('3 months'),
            (string)$intervals[1],
        );
        $this->assertEquals(
            (string)CarbonInterval::years(1)->add('6 months'),
            (string)$intervals[2],
        );
        $this->assertEquals(
            (string)CarbonInterval::years(1)->add('9 months'),
            (string)$intervals[3],
        );
        $this->assertEquals((string)$intervals[4], (string)static::$interval2Y);
    }

    public function testIterateBy3MonthsAlignValueUnit()
    {
        $minInterval = CarbonInterval::instance(static::$interval1Y)->addDays(1);
        $intervals = DateTimeService::getIntervals(
            minInterval: $minInterval,
            maxInterval: static::$interval2Y,
            iterateByValueUnits: '3 months',
            alignValueUnits: true,
        );

        $this->assertCount(5, $intervals);
        $this->assertEquals((string)$intervals[0], (string)$minInterval);
        $this->assertEquals(
            (string)CarbonInterval::years(1)->add('3 months'),
            (string)$intervals[1],
        );
        $this->assertEquals(
            (string)CarbonInterval::years(1)->add('6 months'),
            (string)$intervals[2],
        );
        $this->assertEquals(
            (string)CarbonInterval::years(1)->add('9 months'),
            (string)$intervals[3],
        );
        $this->assertEquals((string)$intervals[4], (string)static::$interval2Y);
    }

    public function testIterateBy3Months2MinutesAnd1SecondAlignValueUnits()
    {
        $minInterval = CarbonInterval::instance(static::$interval1Y)->addDays(2)->addHours(3);
        $intervals = DateTimeService::getIntervals(
            minInterval: $minInterval,
            maxInterval: static::$interval2Y,
            iterateByValueUnits: '2 minutes 3 months 1 second',
            alignValueUnits: true,
        );

        $this->assertCount(5, $intervals);
        $this->assertEquals((string)$intervals[0], (string)$minInterval);
        $this->assertEquals(
            (string)CarbonInterval::years(1)->addMonths(3)->addMinutes(2)->addSeconds(1),
            (string)$intervals[1],
        );
        $this->assertEquals(
            (string)CarbonInterval::years(1)->addMonths(6)->addMinutes(2)->addSeconds(1),
            (string)$intervals[2],
        );
        $this->assertEquals(
            (string)CarbonInterval::years(1)->addMonths(9)->addMinutes(2)->addSeconds(1),
            (string)$intervals[3],
        );
        $this->assertEquals((string)$intervals[4], (string)static::$interval2Y);
    }

    public function testIterateHourlyAlignValueUnits()
    {
        $minInterval = CarbonInterval::seconds(2);
        $maxInterval = CarbonInterval::days(1);
        $intervals = DateTimeService::getIntervals(
            minInterval: $minInterval,
            maxInterval: $maxInterval,
            iterateByValueUnits: '5 hour',
            alignValueUnits: true,
        );

        $this->assertCount(6, $intervals);
        $this->assertEquals((string)$intervals[0], (string)$minInterval);
        $this->assertEquals(
            (string)CarbonInterval::hours(5 * 1),
            (string)$intervals[1],
        );
        $this->assertEquals(
            (string)CarbonInterval::hours(5 * 2),
            (string)$intervals[2],
        );
        $this->assertEquals(
            (string)CarbonInterval::hours(5 * 3),
            (string)$intervals[3],
        );
        $this->assertEquals(
            (string)CarbonInterval::hours(5 * 4),
            (string)$intervals[4],
        );
        $this->assertEquals((string)$intervals[5], (string)$maxInterval);
    }

    public function testIterateYearlyAlignValueUnits()
    {
        $minInterval = CarbonInterval::years(1)->addSeconds(5);
        $maxInterval = CarbonInterval::years(3);
        $intervals = DateTimeService::getIntervals(
            minInterval: $minInterval,
            maxInterval: $maxInterval,
            iterateByValueUnits: '1 year',
            alignValueUnits: true,
        );

        $this->assertCount(3, $intervals);
        $this->assertEquals((string)$intervals[0], (string)$minInterval);
        $this->assertEquals(
            (string)CarbonInterval::years(2),
            (string)$intervals[1],
        );
        $this->assertEquals((string)$intervals[2], (string)$maxInterval);
    }

    public function testIterateYearlyAlignValueUnitsWithMaxRestrictionOutside()
    {
        $minInterval = CarbonInterval::seconds(2);
        $maxInterval = CarbonInterval::days(1);
        $intervals = DateTimeService::getIntervals(
            minInterval: $minInterval,
            maxInterval: $maxInterval,
            iterateByValueUnits: '1 year',
            alignValueUnits: true,
        );

        $this->assertCount(2, $intervals);
        $this->assertEquals((string)$intervals[0], (string)$minInterval);
        $this->assertEquals((string)$intervals[1], (string)$maxInterval);
    }

    public function testSameIntervalsExcluded()
    {
        $intervals = DateTimeService::getIntervals(
            minInterval: static::$interval1Y,
            maxInterval: static::$interval1Y,
            iterateByCount: 2,
        );

        $this->assertCount(1, $intervals);
    }
}
