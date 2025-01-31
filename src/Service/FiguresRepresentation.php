<?php

namespace GrinWay\Service\Service;

use GrinWay\Service\Validator\LikeInt;
use Symfony\Component\Validator\Constraints\NotBlank;
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
        if (!Validation::createIsValidCallable(new NotBlank(), new LikeInt())($startNumber)) {
            $message = \sprintf('Passed $startNumber is not like int, got "%s"', $startNumber);
            throw new \InvalidArgumentException($message);
        }

        if (Validation::createIsValidCallable(new NotBlank(), new LikeInt())($endNumber)) {

            if (0 === $endNumber || u($endNumber)->match('~^(?<only_zeros>0+)$~')['only_zeros'] ?? null) {
                $endNumber = \str_repeat('0', $endFiguresCount);
            } else {
                $passedEndFiguresCount = \strlen((string)$endNumber);

                if ($endFiguresCount < $passedEndFiguresCount) {
                    $endNumber = \substr((string)$endNumber, 0, $endFiguresCount + 1);
                    $endNumber = (int)$endNumber;
                    $endNumber = $endNumber / 10;
                    $endNumber = (int)\round($endNumber, 0, \PHP_ROUND_HALF_UP);// ?
                    \dump($endNumber);
                } elseif ($endFiguresCount > $passedEndFiguresCount) {
                    $endNumber .= \str_repeat('0', $endFiguresCount - $passedEndFiguresCount);
                }
            }
        } else {
            $message = \sprintf('Passed $endNumber is not like int, got "%s"', $endNumber);
            throw new \InvalidArgumentException($message);
        }
        $resultNumberWithEndFigures = \sprintf(
            '%s%s',
            $startNumber,
            $endNumber,
        );
        \assert(Validation::createIsValidCallable(new LikeInt())($resultNumberWithEndFigures));
        return $resultNumberWithEndFigures;
    }
}
