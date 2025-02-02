<?php

namespace GrinWay\Service\Service;

use GrinWay\Service\Validator\LikeInt;
use GrinWay\Service\Validator\LikeNumeric;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Validation;
use function Symfony\Component\String\u;

class FiguresRepresentation
{
    /**
     * API
     *
     * If 2 === $endFiguresCount
     * (float $number 1.) -> 100
     * (float $number 1.0000000000000) -> 100
     * (int $number 23) -> 2300
     * (string $number 0) -> 000
     */
    public static function getStringWithEndFigures(string|int|float $number, int $endFiguresCount): string
    {
        $number = (string)$number;

        self::validate(
            $number,
            [new NotBlank(), new LikeNumeric()],
        );

        $matches = u($number)->match('~^(?<front>\d*)(?:[.](?<end>\d*))?$~');
        $frontFigures = $matches['front'];
        $endFigures = $matches['end'] ?: '0';

        return self::concatNumbersWithCorrectCountOfEndFigures(
            $frontFigures,
            $endFigures,
            $endFiguresCount,
        );
    }

    /**
     * API
     *
     * 100 -> 1.00
     */
    public static function amountWithEndFiguresAsFloat(string $amountWithEndFigures, int $endFiguresCount): float
    {
        self::validate(
            $amountWithEndFigures,
            [new NotBlank(), new LikeInt()],
        );
        self::validate(
            $endFiguresCount,
            [new NotBlank(), new PositiveOrZero()],
        );

        if (0 !== $endFiguresCount) {

            $front = \substr($amountWithEndFigures, 0, -1 * \abs($endFiguresCount));
            $end = \substr($amountWithEndFigures, -1 * \abs($endFiguresCount));

            $float = \sprintf(
                '%s.%s',
                $front,
                $end,
            );
        } else {
            $front = $amountWithEndFigures;

            $float = \sprintf(
                '%s.',
                $front,
            );
        }

        return (float)$float;
    }

    /**
     * API
     *
     * Usage:
     * [$one, $twoZeros] = FiguresRepresentation::getStartEndNumbers(100);
     */
    public static function getStartEndNumbersWithEndFigures(string $number, int $endFiguresCount): array
    {
        return [
            self::getStartNumberWithEndFigures($number, $endFiguresCount),
            self::getEndNumberWithEndFigures($number, $endFiguresCount),
        ];
    }

    /**
     * API
     */
    public static function getStartNumberWithEndFigures(string $number, int $endFiguresCount): int
    {
        return (int)self::getStartFiguresWithEndFigures($number, $endFiguresCount);
    }

    /**
     * API
     */
    public static function getEndNumberWithEndFigures(string $number, int $endFiguresCount): int
    {
        return (int)self::getEndFiguresWithEndFigures($number, $endFiguresCount);
    }

    /**
     * API
     */
    public static function getStartFiguresWithEndFigures(string $number, int $endFiguresCount): string
    {
        self::validate(
            $number,
            [new NotBlank(), new LikeInt()],
        );
        self::validate(
            $endFiguresCount,
            [new NotBlank(), new PositiveOrZero()],
        );

        return \substr($number, 0, \strlen($number) - $endFiguresCount);
    }

    /**
     * API
     */
    public static function getEndFiguresWithEndFigures(string $number, int $endFiguresCount): string
    {
        self::validate(
            $number,
            [new NotBlank(), new LikeInt()],
        );
        self::validate(
            $endFiguresCount,
            [new NotBlank(), new PositiveOrZero()],
        );

        return \substr($number, -1 * $endFiguresCount);
    }

    /**
     * API
     *
     * This method joins number with end figures
     * @return string
     */
    public static function concatNumbersWithCorrectCountOfEndFigures(string|int $startNumber, string|int $endNumber, int $endFiguresCount): string
    {
        self::validate(
            $startNumber,
            [new NotBlank(), new LikeInt()],
        );
        self::validate(
            $endNumber,
            [new NotBlank(), new LikeInt()],
        );

        $endNumberContainsOnlyZeros = null !== (u((string)$endNumber)->match('~^(?<only_zeros>[0]+)$~')['only_zeros'] ?? null);
        if ($endNumberContainsOnlyZeros) {
            $endNumber = \str_repeat('0', $endFiguresCount);
        } else {
            $passedEndFiguresCount = \strlen((string)$endNumber);

            if ($endFiguresCount < $passedEndFiguresCount) {
                $plusSignsPrecision = 1;

                $endNumber = \substr((string)$endNumber, 0, $endFiguresCount + $plusSignsPrecision);
                $endNumber = (int)$endNumber;
                $endNumber = $endNumber / (10 ** $plusSignsPrecision);
                $endNumber = (int)\round($endNumber, 0, \PHP_ROUND_HALF_UP);// ?
            } elseif ($endFiguresCount > $passedEndFiguresCount) {
                $endNumber .= \str_repeat('0', $endFiguresCount - $passedEndFiguresCount);
            }
        }
        $resultNumberWithEndFigures = \sprintf(
            '%s%s',
            $startNumber,
            $endNumber,
        );

        self::validate(
            $resultNumberWithEndFigures,
            [new notBlank(), new LikeInt()],
        );
        return $resultNumberWithEndFigures;
    }

    /**
     * @internal
     */
    protected static function validate(mixed $value, array $constraints): void
    {
        Validation::createCallable(...$constraints)($value);
    }
}
