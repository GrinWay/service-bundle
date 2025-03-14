<?php

namespace GrinWay\Service\Service;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;

/**
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
class DateTimeService
{
    /**
     * API
     *
     * @param ?callable $filterInterval Return true to add interval
     * @return CarbonInterval[]
     */
    public static function getIntervals(
        \DateInterval $minInterval,
        \DateInterval $maxInterval,
        int           $count = 2,
        ?callable     $filterInterval = null,
        // TODO: test and then realize
        bool          $hourByHour = false,
    ): array
    {
        $intervals = [];
        $filterInterval ??= static fn(CarbonInterval $nextInterval): bool => true;
        $minInterval = CarbonInterval::instance($minInterval);
        $maxInterval = CarbonInterval::instance($maxInterval);

        if (1 > $count) {
            $count = 1;
        }

        $i = $count;
        $intervalPart = (int)\abs((($maxInterval->total('seconds') - $minInterval->total('seconds')) / $count));
        $nextInterval = CarbonInterval::instance($minInterval);

        while (0 < $i) {
            $first = $i === $count;
            $last = 1 === $i;
            if ($first) {
                // nothing
            } elseif ($last) {
                $nextInterval = CarbonInterval::instance($maxInterval);
            } else {
                $nextInterval = CarbonInterval::instance($nextInterval)
                    ->add($intervalPart, 'seconds')//
                ;
            }
            $nextInterval = $nextInterval->cascade();
            \assert($nextInterval instanceof CarbonInterval);
            self::addFilteredItem($intervals, $nextInterval, $filterInterval);
            --$i;
        }

        return $intervals;
    }

    /**
     * API
     */
    public static function diffFromNow(
        \DateTimeInterface $dateTime,
    ): CarbonInterval
    {
        $dateTimeUtc = Carbon::instance($dateTime)->setTimezone('UTC');
        return Carbon::now('UTC')->diff($dateTimeUtc);
    }

    /**
     * API
     */
    public static function lessThanNow(
        \DateTimeInterface $dateTime,
    ): bool
    {
        return 0 > self::diffFromNow(dateTime: $dateTime)->total('seconds');
    }

    /**
     * API
     */
    public static function greaterThanNow(
        \DateTimeInterface $dateTime,
    ): bool
    {
        return 0 < self::diffFromNow(dateTime: $dateTime)->total('seconds');
    }

    /**
     * API
     */
    public static function notLessThanNow(
        \DateTimeInterface $dateTime,
    ): bool
    {
        return !self::lessThanNow(dateTime: $dateTime);
    }

    /**
     * API
     */
    public static function getPeriodsOverlapInterval(
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

    /**
     * @internal
     */
    private static function addFilteredItem(array &$items, mixed $item, callable $filter): void
    {
        if (true === $filter($item)) {
            $items[] = $item;
        }
    }
}
