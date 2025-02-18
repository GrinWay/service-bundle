<?php

namespace GrinWay\Service\Tests\Unit\Conversion;

use GrinWay\Service\Service\Currency;
use GrinWay\Service\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Currency::class)]
class SuccessfullyDumpMockedDataToTheNonRemovableCacheTest extends AbstractUnitTestCase
{
    private static string $nonRemovableCurrencyFixerApiCacheLogicalDir;

    public function test()
    {
        self::$nonRemovableCurrencyFixerApiCacheLogicalDir = self::getContainer()
            ->get('kernel')
            ->locateResource(self::getContainer()->getParameter(
                'grinway_service.logical_path.non_removable_cache.currency.fixer_api',
            ))//
        ;
        $this->assertFileExists(self::$nonRemovableCurrencyFixerApiCacheLogicalDir);

        $result = $this->currencyService->convertFromCurrencyToAnotherWithEndFigures(
            '100',
            'RUB',
            'EUR',
            2,
            forceMakeHttpRequestToFixer: true,
        );

        $this->assertNotNull($result);
        $this->assertSame(
            self::$mockedGrinWayServiceFixerLatestClientPlainResponse,
            \file_get_contents(self::$nonRemovableCurrencyFixerApiCacheLogicalDir),
        );
    }
}
