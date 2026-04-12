<?php

namespace GrinWay\Service\Tests\Unit\DateTimeService;

use Carbon\Carbon;
use GrinWay\Service\Service\DateTimeService;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversMethod(DateTimeService::class, 'diffFromNow')]
class DateTimeServiceDiffFromNowTest extends AbstractDateTimeServiceTestCase
{
    public function testNonUtcTimeZoneAndNotMutateOriginDateTime()
    {
        $dateTime = Carbon::now('+12:00'); // NOT UTC
        $diff = DateTimeService::diffFromNow($dateTime);

        $this->assertSame('+12:00', $dateTime->getTimezone()->getName(), message: 'Method supposed not to mutate date time object');
        $this->assertLessThan(60, (int)$diff->totalSeconds); // BUT DIFFERENCE IS STILL LOW
    }
}
