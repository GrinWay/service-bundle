<?php

namespace GrinWay\Service\Service;

use GrinWay\Service\Exception\Fixer\NoBaseFixerException;
use GrinWay\Service\Exception\Fixer\NotSuccessFixerException;
use GrinWay\Service\GrinWayServiceBundle;
use GrinWay\Service\Kernel;
use GrinWay\Service\Validator\LikeInt;
use GrinWay\Service\Validator\LikeNumeric;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\Serializer\SerializerInterface;
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
    public function convertFromCurrencyToAnotherWithEndFigures(string $amountWithEndFigures, string $amountCurrency, string $convertToCurrency, int $endFiguresCount, bool $forceMakeHttpRequestToFixer = false, bool $allowNonRemovableCache = true): string
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

        if ('000' === $amountWithEndFigures || $fromCurrencyString === $toCurrencyString) {
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
            $success = $pa->getValue($fixerPayload, '[success]') ?: false;

            if (false === $success) {
                try {
                    // make sure data was got
                    $fixerPayload = $this->getFixerPayloadFromNonRemovableCache(
                        allowNonRemovableCache: $allowNonRemovableCache,
                    );
                } catch (\Exception $e) {
                }
            }
        }

        $success = $pa->getValue($fixerPayload, '[success]') ?: false;
        if (true === $success) {
            $baseString = $pa->getValue($fixerPayload, '[base]');
            if (null === $baseString) {
                try {
                    $fixerPayload = $this->getFixerPayloadFromNonRemovableCache(
                        allowNonRemovableCache: $allowNonRemovableCache,
                    );
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
                    $this->dumpFixerPayloadToNonRemovableCache(
                        fixerPayload: $fixerPayload,
                        allowNonRemovableCache: $allowNonRemovableCache,
                    );
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
    protected function assertCurrencyExistsInFixerAPI(?string $currencyValue, ?string $currencyString): void
    {
        if (null === $currencyValue) {
            $message = \sprintf(
                'There is no info about (%s)"%s" currency in the fixer API payload',
                \get_debug_type($currencyString),
                $currencyString,
            );
            throw new \RuntimeException($message);
        }
    }

    /**
     * @internal
     */
    protected function getCachedFixerDecodedPayload(bool $refresh = false, bool $allowNonRemovableCache = true): array
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
        $fixerPayload = $currencyCachePool->get($currencyCacheKey, function (ItemInterface $item) use ($allowNonRemovableCache, &$freshFixerPayloadFlagForDumpToNonRemovableCache, $fixerHttpClient): string {
            $freshFixerPayloadFlagForDumpToNonRemovableCache = true;
            $item->tag([GrinWayServiceBundle::GENERIC_CACHE_TAG]);
            try {
                $content = $fixerHttpClient->request('GET', '')->getContent();
            } catch (TransportException $exception) {
                try {
                    $content = $this->getFixerPayloadFromNonRemovableCache(
                        allowNonRemovableCache: $allowNonRemovableCache,
                    );
                } catch (\Exception $e) {
                    $content = '{}';
                }
            } catch (\Exception $exception) {
                throw $exception;
            }
            return $content;
        });

        return $serializer->decode($fixerPayload, 'json');
    }

    /**
     * Helper
     *
     * @internal
     */
    private function getValidatedOneBaseCurrencyValueFromFixerPayload(
        string $currencyString,
        array  $fixerPayload,
    ): mixed
    {
        if (empty($currencyString)) {
            throw new \InvalidArgumentException('Currency string can\'t be empty');
        }

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

    private function dumpFixerPayloadToNonRemovableCache(
        array $fixerPayload,
        bool  $allowNonRemovableCache,
    ): void
    {
        if (false === $allowNonRemovableCache) {
            return;
        }

        /** @var SerializerInterface $serializer */
        $serializer = $this->serviceLocator->get('serializer');
        /** @var Kernel $kernel */
        $kernel = $this->serviceLocator->get('kernel');
        /** @var Filesystem $kernel */
        $filesystem = $this->serviceLocator->get('filesystem');

        $absPathname = $kernel->locateResource($this->nonRemovableCurrencyFixerApiCacheLogicalDir);
        $filesystem->dumpFile($absPathname, $serializer->encode($fixerPayload, 'json'));
    }

    private function getFixerPayloadFromNonRemovableCache(
        bool $allowNonRemovableCache,
    ): array
    {
        if (false === $allowNonRemovableCache) {
            return [];
        }

        /** @var SerializerInterface $serializer */
        $serializer = $this->serviceLocator->get('serializer');
        /** @var Kernel $kernel */
        $kernel = $this->serviceLocator->get('kernel');

        $absPathname = $kernel->locateResource($this->nonRemovableCurrencyFixerApiCacheLogicalDir);
        return $serializer->decode(\file_get_contents($absPathname), 'json');
    }
}
