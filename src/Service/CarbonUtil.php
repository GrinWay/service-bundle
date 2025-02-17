<?php

namespace GrinWay\Service\Service;

use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;

/**
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
class CarbonUtil
{
    /**
     * API
     */
    public static function getPeriodsOverlap(
        CarbonPeriod $periodA,
        CarbonPeriod $periodB,
    ): CarbonInterval
    {
        if (!$periodA->overlaps($periodB)) {
            return new CarbonInterval();
        }

        $firstEndDate = \min($periodA->calculateEnd(), $periodB->calculateEnd());
        $latestStartDate = \max($periodA->getStartDate(), $periodB->getStartDate());

        return CarbonInterval::make($firstEndDate->diff($latestStartDate));
    }
}
