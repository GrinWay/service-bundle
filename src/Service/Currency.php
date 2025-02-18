<?php

namespace GrinWay\Service\Service;

use App\Kernel;
use GrinWay\Service\Exception\Fixer\NoBaseFixerException;
use GrinWay\Service\Exception\Fixer\NotSuccessFixerException;
use GrinWay\Service\GrinWayServiceBundle;
use GrinWay\Service\Validator\LikeInt;
use GrinWay\Service\Validator\LikeNumeric;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Currency converter
 *
 * Telegram currency amount style explanation: (IMAGINED 1.00 becomes REPRESENTED IN VARIABLE AS 100)
 *
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
class Currency
{
    private bool $freshFixerPayloadFlagForDumpToNonRemovableCache = false;

    public function __construct(
        protected readonly ServiceLocator $serviceLocator,
        private readonly string           $nonRemovableCurrencyFixerApiCacheLogicalDir,
    )
    {
    }

    /**
     * @deprecated function "transferAmountFromTo" use "convertFromCurrencyToAnotherWithEndFigures" instead since grinway/service-bundle v2 will be removed
     * @deprecated default value for $endFiguresCount since grinway/service-bundle v2 will be required
     */
    public function transferAmountFromTo(string $amountWithEndFigures, string $amountCurrency, mixed $convertToCurrency, int $endFiguresCount = 2): string
    {
        return $this->convertFromCurrencyToAnotherWithEndFigures(
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
     * @throws NotSuccessFixerException
     * @throws NoBaseFixerException
     */
    public function convertFromCurrencyToAnotherWithEndFigures(string $amountWithEndFigures, string $amountCurrency, mixed $convertToCurrency, int $endFiguresCount, bool $forceMakeHttpRequestToFixer = false): string
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

                try {
                    $fixerPayload = $this->getFixerPayloadFromNonRemovableCache();
                } catch (\Exception $e) {
                    throw new NoBaseFixerException();
                }

                $baseString = $pa->getValue($fixerPayload, '[base]');
                if (null === $baseString) {
                    throw new NoBaseFixerException();
                }
            }

            if (true === $this->freshFixerPayloadFlagForDumpToNonRemovableCache) {
                try {
                    $this->dumpFixerPayloadToNonRemovableCache($fixerPayload);
                } catch (\Exception $e) {
                }
                $this->freshFixerPayloadFlagForDumpToNonRemovableCache = false;
            }

            $oneBaseToCurrencyValue = $this->getValidatedOneBaseCurrencyValueFromFixerPayload(
                $toCurrencyString,
                $fixerPayload,
            );

            if ($baseString === $fromCurrencyString) {
                $oneBaseFromCurrencyValue = 1; // exactly 1 rate
            } else {
                $oneBaseFromCurrencyValue = $this->getValidatedOneBaseCurrencyValueFromFixerPayload(
                    $fromCurrencyString,
                    $fixerPayload,
                );
            }
        } else {
            throw new NotSuccessFixerException();
        }

        // CONVERSION ALGORITHM
        $fromCurrencyFloatAmount = FiguresRepresentation::numberWithEndFiguresAsFloat(
            $amountWithEndFigures,
            $endFiguresCount,
        );
        $fromAmountBaseValue = $fromCurrencyFloatAmount / $oneBaseFromCurrencyValue;
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

        $freshFixerPayloadFlagForDumpToNonRemovableCache =& $this->freshFixerPayloadFlagForDumpToNonRemovableCache;
        $fixerPayload = $currencyCachePool->get($currencyCacheKey, static function (ItemInterface $item) use (&$freshFixerPayloadFlagForDumpToNonRemovableCache, $fixerHttpClient): string {
            $freshFixerPayloadFlagForDumpToNonRemovableCache = true;
            $item->tag([GrinWayServiceBundle::GENERIC_CACHE_TAG]);
            return $fixerHttpClient->request('GET', '')->getContent();
        });

        return $serializer->decode($fixerPayload, 'json');
    }

    /**
     * Helper
     *
     * @internal
     */
    private function getValidatedOneBaseCurrencyValueFromFixerPayload(mixed $currencyString, array $fixerPayload)
    {
        $pa = $this->serviceLocator->get('pa');

        $oneBaseCurrencyValue = $pa->getValue(
            $fixerPayload,
            \sprintf('[rates][%s]', $currencyString),
        );
        $this->assertCurrencyExistsInFixerAPI(
            $oneBaseCurrencyValue,
            $currencyString,
        );
        $this->validate(
            $oneBaseCurrencyValue,
            [new NotBlank(), new LikeNumeric()],
        );
        return $oneBaseCurrencyValue;
    }

    private function dumpFixerPayloadToNonRemovableCache(array $fixerPayload): void
    {
        /** @var \Symfony\Component\Serializer\SerializerInterface $serializer */
        $serializer = $this->serviceLocator->get('serializer');
        /** @var Kernel $kernel */
        $kernel = $this->serviceLocator->get('kernel');
        /** @var Filesystem $kernel */
        $filesystem = $this->serviceLocator->get('filesystem');

        $absPathname = $kernel->locateResource($this->nonRemovableCurrencyFixerApiCacheLogicalDir);
        $filesystem->dumpFile($absPathname, $serializer->encode($fixerPayload, 'json'));
    }

    private function getFixerPayloadFromNonRemovableCache(): array
    {
        /** @var \Symfony\Component\Serializer\SerializerInterface $serializer */
        $serializer = $this->serviceLocator->get('serializer');
        /** @var Kernel $kernel */
        $kernel = $this->serviceLocator->get('kernel');

        $absPathname = $kernel->locateResource($this->nonRemovableCurrencyFixerApiCacheLogicalDir);
        return $serializer->decode(\file_get_contents($absPathname), 'json');
    }
}
