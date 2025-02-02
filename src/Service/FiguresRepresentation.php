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
     * Transforms human number representation to the dot-less representation with end figures count
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

        $humanNumber = \number_format($humanNumber,10,'.','');

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
     * Represents dot-less as a float
     *
     * If it's possible don't use this function
     * because it returns float type
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
     * Explodes dot-less number representation to the "start" and "end" (int)s
     * It's an array of int php types
     *
     * Usage:
     * [$one, $twoZeros] = FiguresRepresentation::getStartEndNumbersWithEndFigures(123, 2);
     * // (int)1 === $one
     * // (int)23 === $twoThree
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
     *
     * Extracts "start" part of the dot-less number representation as int
     *
     * If 2 === $endFiguresCount
     * '123' -> (int)1
     */
    public static function getStartNumberWithEndFigures(string $numberWithEndFigures, int $endFiguresCount): int
    {
        return (int)self::getStartFiguresWithEndFigures($numberWithEndFigures, $endFiguresCount);
    }

    /**
     * API
     *
     * Extracts "end" part of the dot-less number representation as int
     *
     * If 2 === $endFiguresCount
     * '123' -> (int)23
     */
    public static function getEndNumberWithEndFigures(string $numberWithEndFigures, int $endFiguresCount): int
    {
        return (int)self::getEndFiguresWithEndFigures($numberWithEndFigures, $endFiguresCount);
    }

    /**
     * API
     *
     * Extracts "start" part of the dot-less number representation as string
     *
     * If 2 === $endFiguresCount
     * '123' -> '1'
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

        $actualLength = \strlen($numberWithEndFigures);
        if ($actualLength < $endFiguresCount) {
            $message = \sprintf(
                'Too long $endFiguresCount, got "%s", max: "%s"',
                $endFiguresCount,
                $actualLength,
            );
            throw new \InvalidArgumentException($message);
        }

        $length = $actualLength - $endFiguresCount;
        return \substr($numberWithEndFigures, 0, $length);
    }

    /**
     * API
     *
     * Extracts "end" part of the dot-less number representation as string
     *
     * If 2 === $endFiguresCount
     * '123' -> '23'
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
     * This method joins "start" and "end" parts to obtain number with end figures
     *
     * This method treats endNumberPart only as a string
     * because there is no any math operations with it
     *
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

        // cuts lead zeros
        $startNumberPart = (int)$startNumberPart;
        $endNumberPart = (string)$endNumberPart;

        $actualEndNumberPartLength = \strlen($endNumberPart);

        // guarantee $endNumberPart not less than required length
        if ($endFiguresCount > $actualEndNumberPartLength) {
            $endNumberPart .= \str_repeat('0', $endFiguresCount - $actualEndNumberPartLength);
        }
        // cut as string DON'T DEAL WITH FLOAT, it can convert (990 / 10) to 100
        $endNumberPart = \substr($endNumberPart, 0, $endFiguresCount);

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
