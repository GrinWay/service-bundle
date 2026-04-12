<?php

namespace GrinWay\Service\Service;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use function Symfony\Component\String\u;

/**
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
class DateTimeService
{
    /**
     * API
     *
     * @param ?int $iterateByCount Default behaviour, has effect only if below argument is null
     * @param ?string $iterateByValueUnits '1 second', '10 years', ...
     *     used for the CarbonInterval::fromString() method
     * @param ?bool $alignValueUnits has effect only when the previous argument is not null
     * @param ?callable $filterInterval Return true to add an interval
     * @return CarbonInterval[]
     */
    public static function getIntervals(
        \DateInterval $minInterval,
        \DateInterval $maxInterval,
        ?int          $iterateByCount = null,
        ?string       $iterateByValueUnits = null,
        ?bool         $alignValueUnits = null,
        ?callable     $filterInterval = null,
    ): array
    {
        $iterateByCount ??= 2;
        if (1 > $iterateByCount) {
            $iterateByCount = 1;
        }
        $alignValueUnits ??= false;
        $filterInterval ??= static fn(CarbonInterval $nextInterval): bool => true;

        Validation::createCallable(new Assert\Regex(
            '~^(?=[0-9a-z]*)(?:[0-9]+\s*[a-z]+\s*)+$~',
            message: 'Invalid CarbonInterval string, look: \'https://carbon.nesbot.com/docs/\'',
        ))($iterateByValueUnits);

        $intervalPart = null;

        $minInterval = CarbonInterval::instance($minInterval);
        $maxInterval = CarbonInterval::instance($maxInterval);

        $nextInterval = CarbonInterval::instance($minInterval)->cascade();
        if (null === $iterateByValueUnits) {
            $intervalPartSec = (int)\abs((($maxInterval->total('seconds') - $minInterval->total('seconds')) / $iterateByCount));
        } else {
            $intervalPart = CarbonInterval::fromString($iterateByValueUnits)->cascade();
            $intervalPartSec = (int)$intervalPart->total('seconds');
        }

        $idx = $iterateByCount;
        $whileConditionExit = false;
        $whileCondition = static function (CarbonInterval $nextInterval) use (
            $maxInterval,
            $iterateByValueUnits,
            &$idx,
            &$whileConditionExit,
        ): bool {
            if (true === $whileConditionExit) {
                return false;
            }

            if (null === $iterateByValueUnits) {
                return 0 < $idx;
            }
            return $nextInterval->totalSeconds <= $maxInterval->totalSeconds;
        };
        $firstCondition = static function (CarbonInterval $nextInterval) use (
            $iterateByCount,
            &$idx,
        ): bool {
            return $idx === $iterateByCount;
        };
        $lastCondition = static function (CarbonInterval $nextInterval) use (
            $maxInterval,
            $iterateByValueUnits,
            &$idx,
        ): bool {
            if (null === $iterateByValueUnits) {
                return 1 === $idx;
            }
            return $nextInterval->totalSeconds >= $maxInterval->totalSeconds;
        };
        $intervals = [];
        $stringIntervals = [];
        while ($whileCondition($nextInterval)) {
            if ($firstCondition($nextInterval)) {
                // nothing
            } else {
                $nextInterval = CarbonInterval::instance($nextInterval)
                    ->add($intervalPartSec, 'seconds')->cascade()//
                ;
                static::alignValueUnit(
                    minInterval: $minInterval,
                    intervalPart: $intervalPart,
                    nextInterval: $nextInterval,
                    iterateByValueUnits: $iterateByValueUnits,
                    alignValueUnit: $alignValueUnits,
                );
            }

            if ($lastCondition($nextInterval)) {
                $nextInterval = CarbonInterval::instance($maxInterval);
                $whileConditionExit = true;
            }

            $nextInterval = $nextInterval->cascade();
            \assert($nextInterval instanceof CarbonInterval);
            $stringInterval = (string)$nextInterval;
            if (!\in_array($stringInterval, $stringIntervals)) {
                static::addFilteredItem($intervals, $nextInterval, $filterInterval);
            }
            $stringIntervals[] = $stringInterval;
            --$idx;
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
        return 0 > static::diffFromNow(dateTime: $dateTime)->total('seconds');
    }

    /**
     * API
     */
    public static function notLessThanNow(
        \DateTimeInterface $dateTime,
    ): bool
    {
        return !static::lessThanNow(dateTime: $dateTime);
    }

    /**
     * API
     */
    public static function greaterThanNow(
        \DateTimeInterface $dateTime,
    ): bool
    {
        return 0 < static::diffFromNow(dateTime: $dateTime)->total('seconds');
    }

    /**
     * API
     */
    public static function notGreaterThanNow(
        \DateTimeInterface $dateTime,
    ): bool
    {
        return !static::greaterThanNow(dateTime: $dateTime);
    }

    /**
     * API
     */
    public static function getPeriodsOverlapInterval(
        CarbonPeriod $period1,
        CarbonPeriod $period2,
    ): CarbonInterval
    {
        if (!$period1->overlaps($period2)) {
            return new CarbonInterval();
        }

        $firstEndDate = \min($period1->calculateEnd(), $period2->calculateEnd());
        $latestStartDate = \max($period1->getStartDate(), $period2->getStartDate());

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

    private static function alignValueUnit(
        CarbonInterval  $minInterval,
        ?CarbonInterval $intervalPart,
        CarbonInterval  &$nextInterval,
        ?string         $iterateByValueUnits,
        ?bool           $alignValueUnit,
    ): void
    {
        if (null !== $iterateByValueUnits && null !== $intervalPart && true === $alignValueUnit) {
            $allValueUnits = \explode(
                ' ',
                (string)u(\trim($iterateByValueUnits))->collapseWhitespace(),
            );

            $nextAlignedInterval = CarbonInterval::instance($nextInterval);
            $startLessUnit = false;
            foreach ([
                         'year',
                         'month',
                         'day',
                         'hour',
                         'minute',
                         'second',
                     ] as $name) {
                if (false === $startLessUnit) {
                    $nameContainsIterateByValueUnits = \in_array(
                            $name,
                            $allValueUnits,
                        ) || \in_array(
                            \sprintf('%s%s', $name, 's'),
                            $allValueUnits,
                        );
                    if ($nameContainsIterateByValueUnits) {
                        $startLessUnit = true;
                    }
                    continue;
                }
                [$nextAlignedInterval, $name]($intervalPart->$name);
            }
            if ($nextAlignedInterval->totalSeconds >= $minInterval->totalSeconds) {
                $nextInterval = $nextAlignedInterval;
            }
        }
    }
}
