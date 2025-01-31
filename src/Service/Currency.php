<?php

namespace GrinWay\Service\Service;

use GrinWay\Service\GrinWayServiceBundle;
use GrinWay\Service\Validator\LikeInt;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Cache\ItemInterface;
use function Symfony\Component\String\u;

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
     * @param string $amountWithEndFigures has Telegram currency amount style (see above explanation)
     *
     * @param string $amountCurrency ISO 4217 Code (Three capital letters)
     * @param string $convertToCurrency ISO 4217 Code (Three capital letters)
     * @return string converted currency value with end figures count equals to param $endFiguresCount
     *
     * @deprecated $endFiguresCount default is 2, in v2 will be required value
     */
    public function transferAmountFromTo(string $amountWithEndFigures, string $amountCurrency, mixed $convertToCurrency, int $endFiguresCount = 2): string
    {
        if (!Validation::createIsValidCallable(new LikeInt())($amountWithEndFigures)) {
            $mesage = \sprintf('The $amountWithEndFigures is not like int, got: "%s"', $amountWithEndFigures);
            throw new \InvalidArgumentException($mesage);
        }

        if (0 > $endFiguresCount) {
            $message = \sprintf(
                '$endFiguresCount can\'t be less than 0, got: "%s"',
                $endFiguresCount,
            );
            throw new \InvalidArgumentException($message);
        }

        $fromCurrency = $amountCurrency;
        $toCurrency = $convertToCurrency;

        if ($fromCurrency === $toCurrency) {
            return $amountWithEndFigures;
        }

        $pa = $this->serviceLocator->get('pa');
        $fixerHttpClient = $this->serviceLocator->get('grinwayServiceCurrencyFixerLatest');
        $serializer = $this->serviceLocator->get('serializer');
        $validator = $this->serviceLocator->get('validator');

        // try to get cached value
        $currencyCachePool = $this->serviceLocator->get('currencyCachePool');
        $fixerPayload = $currencyCachePool->get(GrinWayServiceBundle::bundlePrefixed('fixer_currencies'), function (ItemInterface $item) use ($fixerHttpClient): string {
            $item->tag([GrinWayServiceBundle::CACHE_TAG_FOR_ALL]);
            return $fixerHttpClient->request('GET', '')->getContent();
        });
        $fixerPayload = $serializer->decode($fixerPayload, 'json');
        \dump($fixerPayload);

        $success = $pa->getValue($fixerPayload, '[success]');

        if (true === $success) {

            $base = $pa->getValue($fixerPayload, '[base]');
            if (null === $base) {
                $message = 'There is no base currency form api';
                throw new \RuntimeException($message);
            }

            $toCurrencyRate = $pa->getValue($fixerPayload, \sprintf('[rates][%s]', $toCurrency));
            if (null === $toCurrencyRate) {
                $message = \sprintf(
                    'Can\'t convert to currency "%s"',
                    $toCurrency,
                );
                throw new \RuntimeException($message);
            }
            $validator->validate($toCurrencyRate, [new LikeInt()]);

            if ($base === $fromCurrency) {
                $toNumber = $toCurrencyRate;
            } else {
                $fromCurrencyRate = $pa->getValue($fixerPayload, \sprintf('[rates][%s]', $fromCurrency));
                if (null === $fromCurrencyRate) {
                    $message = \sprintf(
                        'Can\'t convert from currency "%s"',
                        $fromCurrency,
                    );
                    throw new \RuntimeException($message);
                }
                $validator->validate($fromCurrencyRate, [new LikeInt()]);

                $fromCurrencyRate = (float)$fromCurrencyRate;
                $toCurrencyRate = (float)$toCurrencyRate;

                $toNumber = $fromCurrencyRate / $toCurrencyRate;
            }
        } else {
            throw new \LogicException('Request was not successful');
        }

        return $this->getCurrencyValueWithEndTwoFigures($toNumber, $endFiguresCount);
        return '20000';
    }

    private function getCurrencyValueWithEndTwoFigures(string|int $number, int $endFiguresCount): string
    {
        $number = (string)$number;
        $pa = $this->serviceLocator->get('pa');

        $matches = u($number)->match('~^(?<front>\d*)(?:[.](?<end>\d*))?$~');
        \dump($matches);

        $frontFigures = $pa->getValue($matches, '[front]') ?: '0';
        $endFigures = $pa->getValue($matches, '[end]') ?: '0';

        return FiguresRepresentation::concatNumbersWithCorrectCountOfEndFigures($frontFigures, $endFigures, $endFiguresCount);;
    }
}
