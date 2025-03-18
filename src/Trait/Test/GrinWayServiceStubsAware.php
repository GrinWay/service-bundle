<?php

namespace GrinWay\Service\Trait\Test;

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

        $currencyFixerPayload = static::getContainer()
            ->get('grinway_service.currency.fixer_latest')
            ->request('GET', '')
            ->getContent()//
        ;
        $fixerArrayPayload = static::getContainer()
            ->get('serializer')
            ->decode($currencyFixerPayload, 'json')//
        ;
        if (null === ($fixerArrayPayload['grinway_key_fake_fixer'] ?? null)) {
            $message = '!!! Accidentally used a real fixer API service, MOCK IT !!!';
            echo $message . \PHP_EOL . \PHP_EOL;
            throw new \RuntimeException($message);
        }

        static::$currencyService = static::getContainer()->get('grinway_service.currency');
    }
}
