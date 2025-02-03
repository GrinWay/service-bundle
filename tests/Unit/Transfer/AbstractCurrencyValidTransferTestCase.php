<?php

namespace GrinWay\Service\Tests\Unit\Transfer;

use GrinWay\Service\Tests\Unit\AbstractUnitTestCase;

abstract class AbstractCurrencyValidTransferTestCase extends AbstractUnitTestCase
{
    abstract public static function fromCurrency(): string;

    abstract public static function amount(): string;

    abstract public static function validForStaticPayloadTransferredAmounts(): \Generator;

    public function testTransferAmountFromToWithEndFiguresValidConversion()
    {
        $toCurrencies = \array_values($this->allCurrencies);

        $validAmountGenerator = static::validForStaticPayloadTransferredAmounts();
        foreach ($toCurrencies as $idx => $toCurrency) {
            $fromCurrency = static::fromCurrency();
            $amountWithEndFigures = static::amount();
            $transferredAmount = $this->currencyService->convertFromCurrencyToAnotherWithEndFigures(
                $amountWithEndFigures,
                $fromCurrency,
                $toCurrency,
                2,
                forceMakeHttpRequestToFixer: true, // force use a fake http client mocked before
            );
            $this->assertSame(
                $validAmountGenerator->current(),
                $transferredAmount,
            );
//            \dump(\sprintf('%s%s', $fromCurrency, $transferredAmount));

            if ($validAmountGenerator->valid()) {
                $validAmountGenerator->next();
            }
        }
    }
}
