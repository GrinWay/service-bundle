<?php

namespace GrinWay\Service\Service;

use GrinWay\Service\Validator\LikeInt;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Validation;
use function Symfony\Component\String\u;

class FiguresRepresentation
{
    /**
     * This method joins number with end figures
     * @return string
     */
    public static function concatNumbersWithCorrectCountOfEndFigures(string|int $startNumber, string|int $endNumber, int $endFiguresCount): string
    {
        self::validate(
            $startNumber,
            [new NotBlank(), new LikeInt()],
            'Passed $startNumber is not like int',
        );
        self::validate(
            $endNumber,
            [new NotBlank(), new LikeInt()],
            'Passed $endNumber is not like int',
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
            'Result string must be not blank, like int',
        );
        return $resultNumberWithEndFigures;
    }

    public static function amountWithEndFiguresAsFloat(string $amountWithEndFigures, int $endFiguresCount): float
    {
        self::validate(
            $amountWithEndFigures,
            [new NotBlank(), new LikeInt()],
            '$amountWithEndFigures must be not blank and like int'
        );
        self::validate(
            $endFiguresCount,
            [new NotBlank(), new PositiveOrZero()],
            '$endFiguresCount must be not blank and positive or zero'
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

    private static function validate(mixed $value, array $constraints, string $invalidArgumentExceptionString): void
    {
        if (!Validation::createIsValidCallable(...$constraints)($value)) {
            if (empty($invalidArgumentExceptionString)) {
                $message = 'Invalid value, got "%s"';
            } else {
                $invalidArgumentExceptionString = \rtrim($invalidArgumentExceptionString, '.,');
                $message = \sprintf('%s, got "%s"', $invalidArgumentExceptionString, $value);
            }
            throw new \InvalidArgumentException($message);
        }
    }
}
