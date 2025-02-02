<?php

namespace GrinWay\Service\Service;

use GrinWay\Service\Validator\LikeInt;
use GrinWay\Service\Validator\LikeNumeric;
use GrinWay\Telegram\Service\Telegram;
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
     *
     * It's not recommended but even (string $number 1,0) -> 100
     */
    public static function getStringWithEndFigures(string|int|float $humanNumber, int $endFiguresCount): string
    {
        $humanNumber = (string)$humanNumber;
        $humanNumber = \strtr($humanNumber, [',' => '.']);

        self::validate(
            $humanNumber,
            [new NotBlank(), new LikeNumeric()],
        );

        $matches = u($humanNumber)->match('~^(?<front>\d*)(?:[.](?<end>\d*))?$~');
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
    public static function numberWithEndFiguresAsFloat(string $numberWithEndFigures, int $endFiguresCount): float
    {
        self::validate(
            $numberWithEndFigures,
            [new NotBlank(), new LikeInt()],
        );
        self::validate(
            $endFiguresCount,
            [new NotBlank(), new PositiveOrZero()],
        );

        if (0 !== $endFiguresCount) {

            $front = \substr($numberWithEndFigures, 0, -1 * \abs($endFiguresCount));
            $end = \substr($numberWithEndFigures, -1 * \abs($endFiguresCount));

            $float = \sprintf(
                '%s.%s',
                $front,
                $end,
            );
        } else {
            $front = $numberWithEndFigures;

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
    public static function getStartEndNumbersWithEndFigures(string $numberWithEndFigures, int $endFiguresCount): array
    {
        return [
            self::getStartNumberWithEndFigures($numberWithEndFigures, $endFiguresCount),
            self::getEndNumberWithEndFigures($numberWithEndFigures, $endFiguresCount),
        ];
    }

    /**
     * API
     */
    public static function getStartNumberWithEndFigures(string $numberWithEndFigures, int $endFiguresCount): int
    {
        return (int)self::getStartFiguresWithEndFigures($numberWithEndFigures, $endFiguresCount);
    }

    /**
     * API
     */
    public static function getEndNumberWithEndFigures(string $numberWithEndFigures, int $endFiguresCount): int
    {
        return (int)self::getEndFiguresWithEndFigures($numberWithEndFigures, $endFiguresCount);
    }

    /**
     * API
     */
    public static function getStartFiguresWithEndFigures(string $numberWithEndFigures, int $endFiguresCount): string
    {
        self::validate(
            $numberWithEndFigures,
            [new NotBlank(), new LikeInt()],
        );
        self::validate(
            $endFiguresCount,
            [new NotBlank(), new PositiveOrZero()],
        );

        return \substr($numberWithEndFigures, 0, \strlen($numberWithEndFigures) - $endFiguresCount);
    }

    /**
     * API
     */
    public static function getEndFiguresWithEndFigures(string $numberWithEndFigures, int $endFiguresCount): string
    {
        self::validate(
            $numberWithEndFigures,
            [new NotBlank(), new LikeInt()],
        );
        self::validate(
            $endFiguresCount,
            [new NotBlank(), new PositiveOrZero()],
        );

        return \substr($numberWithEndFigures, -1 * $endFiguresCount);
    }

    /**
     * API
     *
     * This method joins number with end figures
     * @return string
     */
    public static function concatNumbersWithCorrectCountOfEndFigures(string|int $startNumberPart, string|int $endNumberPart, int $endFiguresCount): string
    {
        self::validate(
            $startNumberPart,
            [new NotBlank(), new LikeInt()],
        );
        self::validate(
            $endNumberPart,
            [new NotBlank(), new LikeInt()],
        );

        // '000000000000000000000000' -> 0
        // '999999999999999999999999999999999999999' -> no more than max int
        $endNumberPart = (int)\substr(
            $endNumberPart,
            0,
            \strlen((string)\PHP_INT_MAX) - 1,
        );

        $actualEndNumberPartLength = \strlen((string)$endNumberPart);

        $signsPrecision = 1;
        $requiredForRoundingLengthEndNumberPart = $endFiguresCount + $signsPrecision;

        // guarantee $endNumberPart not less than required length
        if ($requiredForRoundingLengthEndNumberPart > $actualEndNumberPartLength) {
            $endNumberPart .= \str_repeat('0', $requiredForRoundingLengthEndNumberPart - $actualEndNumberPartLength);
        }
        // cut as string
        $endNumberPart = (int)\substr($endNumberPart, 0, $requiredForRoundingLengthEndNumberPart);
        // cut as int
        $endNumberPart /= 10 ** ($requiredForRoundingLengthEndNumberPart - $signsPrecision);
        // round
        $endNumberPart = (int)\round($endNumberPart, 0, \PHP_ROUND_HALF_UP);

        $firstNumberOfEndNumberPart = (int)($endNumberPart / (10 ** $endFiguresCount));
        $endNumberPartOverflow = 0 !== $firstNumberOfEndNumberPart;
        // 100 === $endNumberPart for instance
        if ($endNumberPartOverflow) {
            $startNumberPart += $firstNumberOfEndNumberPart;
            $endNumberPart %= 10 ** $endFiguresCount;
        }

        $resultNumberWithEndFigures = \sprintf(
            '%s%s',
            $startNumberPart,
            $endNumberPart,
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
