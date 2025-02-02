<?php

namespace GrinWay\Service\Tests\Unit;

class CurrencyServiceTest
{
    /**
     * Converter checker
     */
    private function assertValid1USDtoRUBConversion()
    {
        $mustBeRubStartOfOneDollar = 98;
        $mustBeRubEndOfOneDollar = 56;
        if ($mustBeRubStartOfOneDollar !== $this->rubStartOneDollarInt
            || $mustBeRubEndOfOneDollar !== $this->rubEndOneDollarInt
        ) {
            echo \sprintf(
                '!!! INVALID USD TO RUB CONVERSION!!!%s
payload is mocked with static payload data %s
start rub of 1 dollar must be %s, got %s
end rub of 1 dollar must be %s, got %s
%s%s',
                \PHP_EOL,
                \PHP_EOL,
                $mustBeRubStartOfOneDollar,
                $this->rubStartOneDollarInt,
                $mustBeRubEndOfOneDollar,
                $this->rubEndOneDollarInt,
                \PHP_EOL,
                \PHP_EOL,
            );
            exit;
        }
    }
}
