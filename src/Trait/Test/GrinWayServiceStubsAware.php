<?php

namespace GrinWay\Service\Trait\Test;

use GrinWay\Service\Contract\Test\TestKey;
use GrinWay\Service\Service\Currency;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

trait GrinWayServiceStubsAware
{
    protected static Currency $currencyService;

    abstract protected static function isStubCurrencyFixerLatest(): bool;

    abstract protected static function getStubCurrencyFixerLatestResponseBody(): string;

    protected static function setUpGrinWayServiceMockedDependencies(): void
    {
        if (true === static::isStubCurrencyFixerLatest()) {
            $grinwayTelegramFileClientResponseGenerator = static function (): \Generator {
                while (true) {
                    yield new MockResponse(static::getStubCurrencyFixerLatestResponseBody());
                }
            };
            static::getContainer()->set('grinway_service.currency.fixer_latest',
                new MockHttpClient(
                    $grinwayTelegramFileClientResponseGenerator(),
                ),
            );
        }

        $serviceId = 'grinway_service.currency.fixer_latest';
        $currencyFixerPayload = static::getContainer()
            ->get($serviceId)
            ->request('GET', '')
            ->getContent()//
        ;
        $fixerArrayPayload = static::getContainer()
            ->get('serializer')
            ->decode($currencyFixerPayload, 'json')//
        ;
        if (null === ($fixerArrayPayload[TestKey::GRINWAY_FAKE_FIXER_KEY] ?? null)) {
            $message = \sprintf(
                '!!! Mocked "%s" must have "%s" key in its mocked body to distinguish if it\'s mocked or not !!!%sTo do this return correct http client response body in the abstract method',
                $serviceId,
                TestKey::GRINWAY_FAKE_FIXER_KEY,
                \PHP_EOL,
            );
            echo $message . \PHP_EOL . \PHP_EOL;
            throw new \RuntimeException($message);
        }

        static::$currencyService = static::getContainer()->get('grinway_service.currency');
    }
}
