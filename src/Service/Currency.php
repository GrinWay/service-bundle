<?php

namespace GrinWay\Service\Service;

use GrinWay\Service\GrinWayServiceBundle;
use GrinWay\Service\Validator\LikeInt;
use GrinWay\Service\Validator\LikeNumeric;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Currency converter
 *
 * Telegram currency amount style explanation: (IMAGINED 1.00 becomes REPRESENTED IN VARIABLE AS 100)
 */
class Currency
{
    public function __construct(protected readonly ServiceLocator $serviceLocator)
    {
    }

    /**
     * @deprecated function "transferAmountFromTo" use "transferAmountFromToWithEndFigures" instead since grinway/service-bundle v2 will be removed
     * @deprecated default value for $endFiguresCount since grinway/service-bundle v2 will be required
     */
    public function transferAmountFromTo(string $amountWithEndFigures, string $amountCurrency, mixed $convertToCurrency, int $endFiguresCount = 2): string
    {
        return $this->transferAmountFromToWithEndFigures(
            $amountWithEndFigures,
            $amountCurrency,
            $convertToCurrency,
            $endFiguresCount,
        );
    }

    /**
     * API
     *
     * @param string $amountWithEndFigures has Telegram currency amount style (see above explanation)
     *
     * @param string $amountCurrency ISO 4217 Code (Three capital letters)
     * @param string $convertToCurrency ISO 4217 Code (Three capital letters)
     * @return string converted currency value with end figures (with count $endFiguresCount)
     */
    public function transferAmountFromToWithEndFigures(string $amountWithEndFigures, string $amountCurrency, mixed $convertToCurrency, int $endFiguresCount, bool $forceMakeHttpRequestToFixer = false): string
    {
        $this->validate(
            $amountWithEndFigures,
            [new NotBlank(), new LikeInt()],
            \InvalidArgumentException::class,
        );
        $this->validate(
            $endFiguresCount,
            [new NotBlank(), new PositiveOrZero()],
            \InvalidArgumentException::class,
        );

        $fromCurrencyString = $amountCurrency;
        $toCurrencyString = $convertToCurrency;

        if ($fromCurrencyString === $toCurrencyString) {
            return $amountWithEndFigures;
        }

        $pa = $this->serviceLocator->get('pa');

        // try to get cached value
        if (false === $forceMakeHttpRequestToFixer) {
            $fixerPayload = $this->getCachedFixerDecodedPayload();
        } else {
            $fixerPayload = $this->getCachedFixerDecodedPayload(refresh: true);
        }
        $success = $pa->getValue($fixerPayload, '[success]') ?: false;
        if (false === $success) {
            $fixerPayload = $this->getCachedFixerDecodedPayload(refresh: true);
        }

        if (true === $success) {
            $baseString = $pa->getValue($fixerPayload, '[base]');
            if (null === $baseString) {
                $message = 'There is no base currency form fixer API, invalid response from fixer API';
                throw new \RuntimeException($message);
            }

            $oneBaseToCurrencyValue = $pa->getValue(
                $fixerPayload,
                \sprintf('[rates][%s]', $toCurrencyString),
            );
            $this->assertCurrencyExistsInFixerAPI(
                $oneBaseToCurrencyValue,
                $toCurrencyString,
            );
            $this->validate(
                $oneBaseToCurrencyValue,
                [new NotBlank(), new LikeNumeric()],
            );

            if ($baseString === $fromCurrencyString) {
                $oneBaseFromCurrencyValue = 1; // exactly 1 rate
            } else {
                $oneBaseFromCurrencyValue = $pa->getValue(
                    $fixerPayload,
                    \sprintf('[rates][%s]', $fromCurrencyString),
                );
                $this->assertCurrencyExistsInFixerAPI(
                    $oneBaseToCurrencyValue,
                    $fromCurrencyString,
                );
            }
        } else {
            throw new \RuntimeException('Request to the fixer API service was not successful');
        }

        $this->validate(
            $oneBaseToCurrencyValue,
            [new NotBlank(), new LikeNumeric()],
        );

        $oneBaseFromCurrencyValue = (float)$oneBaseFromCurrencyValue;
        $fromCurrencyFloatAmount = FiguresRepresentation::numberWithEndFiguresAsFloat(
            $amountWithEndFigures,
            $endFiguresCount,
        );
        $fromAmountBaseValue = $fromCurrencyFloatAmount / $oneBaseFromCurrencyValue;

        $oneBaseToCurrencyValue = (float)$oneBaseToCurrencyValue;
        $toNumber = $oneBaseToCurrencyValue * $fromAmountBaseValue;

        return FiguresRepresentation::getStringWithEndFigures(
            $toNumber,
            $endFiguresCount,
        );
    }

    /**
     * Validator helper
     */
    protected function validate(mixed $value, array $constraints, string $exceptionClass = \RuntimeException::class): void
    {
        $errors = $this->serviceLocator->get('validator')->validate($value, $constraints);
        if (\count($errors) > 0) {
            throw new $exceptionClass((string)$errors);
        }
    }

    /**
     * Helper
     */
    protected function assertCurrencyExistsInFixerAPI(?string $currencyValue, string $currencyString): void
    {
        if (null === $currencyValue) {
            $message = \sprintf(
                'There is no info about "%s" currency in the fixer API payload',
                $currencyString,
            );
            throw new \RuntimeException($message);
        }
    }

    /**
     * @internal
     */
    protected function getCachedFixerDecodedPayload(bool $refresh = false): array
    {
        $serializer = $this->serviceLocator->get('serializer');
        $fixerHttpClient = $this->serviceLocator->get('grinwayServiceCurrencyFixerLatest');

        $currencyCacheKey = GrinWayServiceBundle::bundlePrefixed('fixer_currencies');

        /** @var CacheInterface $currencyCachePool */
        $currencyCachePool = $this->serviceLocator->get('currencyCachePool');

        if (true === $refresh) {
            $currencyCachePool->delete($currencyCacheKey);
        }

        $fixerPayload = $currencyCachePool->get($currencyCacheKey, static function (ItemInterface $item) use ($fixerHttpClient): string {
            $item->tag([GrinWayServiceBundle::GENERIC_CACHE_TAG]);
            return $fixerHttpClient->request('GET', '')->getContent();
        });

        return $serializer->decode($fixerPayload, 'json');
    }
}
