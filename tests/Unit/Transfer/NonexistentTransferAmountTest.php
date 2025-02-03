<?php

namespace GrinWay\Service\Tests\Unit\Transfer;

use GrinWay\Service\Service\Currency;
use GrinWay\Service\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Currency::class)]
class NonexistentTransferAmountTest extends AbstractUnitTestCase
{
    public function testTransferringWithNonexistentFromCurrencyThrowsRuntimeException()
    {
        $this->expectException(\RuntimeException::class);
        $this->currencyService->convertFromCurrencyToAnotherWithEndFigures(
            '100',
            'NONEXISTENT_CURRENCY',
            'RUB',
            2,
        );
    }

    public function testTransferringWithNonexistentToCurrencyThrowsRuntimeException()
    {
        $this->expectException(\RuntimeException::class);
        $this->currencyService->convertFromCurrencyToAnotherWithEndFigures(
            '100',
            'EUR',
            'NONEXISTENT_CURRENCY',
            2,
        );
    }

    public function testTransferringWithNonexistentBothDifferentCurrenciesThrowRuntimeException()
    {
        $this->expectException(\RuntimeException::class);
        $this->currencyService->convertFromCurrencyToAnotherWithEndFigures(
            '100',
            'NONEXISTENT_CURRENCY_2',
            'NONEXISTENT_CURRENCY_1',
            2,
        );
    }

    public function testTransferringWithNonexistentBothSameCurrenciesReturnsPassedNumberWithEndFigures()
    {
        $result = $this->currencyService->convertFromCurrencyToAnotherWithEndFigures(
            '100',
            'NONEXISTENT_CURRENCY_1',
            'NONEXISTENT_CURRENCY_1',
            2,
        );
        $this->assertSame('100', $result);
    }
}
