<?php

namespace GrinWay\Service\Service;

use Carbon\{
    Carbon,
    Factory,
    FactoryImmutable,
    CarbonImmutable
};
use Symfony\Component\Filesystem\{
    Path,
    Filesystem
};
use Symfony\Component\Yaml\{
    Tag\TaggedValue,
    Yaml
};
use Symfony\Component\HttpFoundation\{
    Request,
    RequestStack,
    Session\Session
};
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use GrinWay\Service\Contracts\{
    GrinWayIsoFormat
};
use GrinWay\Service\Service\BoolService;
use GrinWay\Service\IsoFormat\{
    GrinWayLLLIsoFormat
};

class CarbonService
{
    public function __construct(
        #[Autowire(service: 'grin_way_service.carbon_factory_immutable')]
        protected $grinWayServiceCarbonFactoryImmutable,
    ) {
    }

    //###> API ###

    /*
        Gets string by Carbon
    */
    public static function isoFormat(
        Carbon|CarbonImmutable $carbon,
        ?GrinWayIsoFormat $isoFormat = null,
        bool $isTitle = true,
    ): string {
        $isoFormat ??= new GrinWayLLLIsoFormat();
        $tz = $carbon->tz;

        return (string) u($carbon->isoFormat($isoFormat::get()) . ' [' . $tz . ']')->title($isTitle);
    }

    /*
        Gets new Carbon by $origin Carbon
    */
    public static function forUser(
        Carbon|CarbonImmutable $origin,
        \DateTimeImmutable|\DateTime $sourceOfMeta = null,
        ?string $tz = null,
        ?string $locale = null,
    ): Carbon|CarbonImmutable {
        $carbonClone = ($origin instanceof Carbon) ? $origin->clone() : $origin;
        return $sourceOfMeta ?
            $carbonClone->tz($sourceOfMeta->tz)->locale($sourceOfMeta->locale) :
            $carbonClone->tz($tz ?? $carbonClone->tz)->locale($locale ?? $carbonClone->locale)
        ;
    }

    /*
        Gets number of the current year
    */
    public function getCurrentYear(): string|int
    {
        $carbon = $this->grinWayServiceCarbonFactoryImmutable->make(
            Carbon::now('UTC'),
        );
        return $carbon->year;
    }

    /* MMMM month as a word */
    public function getCurrentMonthWord(): string
    {
        return $this->grinWayServiceCarbonFactoryImmutable
            ->make(Carbon::now('UTC'))
            ->isoFormat('MMMM')
        ;
    }

    /* MMMM month as a word */
    public function getNextMonthWord(): string
    {
        return $this->grinWayServiceCarbonFactoryImmutable
            ->make(Carbon::now('UTC'))
            ->addMonthsNoOverflow(1)
            ->isoFormat('MMMM')
        ;
    }

    /*
        May throw \Exception
    */
    public function getMonthWordByNumber(
        int|string $monthNumber,
    ): string {
        $monthWord = null;

        try {
            $carbon = $this->grinWayServiceCarbonFactoryImmutable
                ->make(Carbon::now('UTC'))
                ->month($monthNumber)
            ;
        } catch (\Exception $e) {
            throw new \Exception('Не корректное значение числа месяца для ' . Carbon::class . ': ' . $monthNumber);
        }

        $monthWord = $carbon->isoFormat('MMMM');

        if ($monthWord === null) {
            throw new \Exception('Месяц не распознан из номера месяца: ' . $monthNumber);
        }

        return $monthWord;
    }

    //###< API ###


    //###> STATIC API ###

    /**
    * Works as a filter
    *
    * get days... from carbonStart to carbonEnd
    * get years... from carbonStart to carbonEnd
    */
    public static function get(Carbon|CarbonImmutable|callable $carbonStart, Carbon|CarbonImmutable|callable $carbonEnd, ?callable $predicatAddCarbon = null, ?\DateTimeInterface $carbon = null, string $onlyCarbonProperty = null, bool $includePassed = true): array
    {
        $predicatAddCarbon ??= true;

        return self::doGet(
            predicatAddCarbon: $predicatAddCarbon,
            carbon: $carbon,
            onlyCarbonProperty: $onlyCarbonProperty,
            includePassed: $includePassed,
            carbonStart: $carbonStart,
            carbonEnd: $carbonEnd,
        );
    }

    /**
    *
    */
    public static function getWeekends(Carbon|CarbonImmutable|callable $carbonStart, Carbon|CarbonImmutable|callable $carbonEnd, ?\DateTimeInterface $carbon = null, string $onlyCarbonProperty = null, bool $includePassed = true): array
    {
        return self::doGet(
            predicatAddCarbon: static fn($c) => $c->isWeekend(),
            carbon: $carbon,
            onlyCarbonProperty: $onlyCarbonProperty,
            includePassed: $includePassed,
            carbonStart: $carbonStart,
            carbonEnd: $carbonEnd,
        );
    }

    /**
    *
    */
    public static function getNow(
        bool $isImmutable = true,
        string $tz = 'UTC',
    ): Carbon|CarbonImmutable {
        return $isImmutable ? CarbonImmutable::now($tz) : Carbon::now($tz);
    }

    //###< STATIC API ###

    private static function doGet(Carbon|CarbonImmutable|callable $carbonStart, Carbon|CarbonImmutable|callable $carbonEnd, ?\DateTimeInterface $carbon, string $onlyCarbonProperty, bool $includePassed, callable|bool $predicatAddCarbon, ?callable $carbonGetNext = null): array
    {
        $carbon = static::getEnsuredCarbonInstance($carbon, isImmutable: true);

        $carbonGetNext ??= $onlyCarbonProperty;

        if (!\is_callable($carbonStart)) {
            $mess = \sprintf('You must pass an argument: "%s"', '$carbonStart');
            throw new \Exception($mess);
            //$carbonStart = $carbonStart->startOfMonth();
        } else {
            $carbonStart = $carbonStart($carbon);
        }

        if (!\is_callable($carbonEnd)) {
            $mess = \sprintf('You must pass an argument: "%s"', '$carbonEnd');
            throw new \Exception($mess);
            //$carbonEnd = $carbonEnd->endOfMonth();
        } else {
            $carbonEnd = $carbonEnd($carbon);
        }

        $arrayOfCarbon = static::getArrayOfCarbonCallback(
            carbon: $carbon,
            carbonStart: $carbonStart,
            carbonEnd: $carbonEnd,
            carbonGetNext: $carbonGetNext,
            includePassed: $includePassed,
            predicatAddCarbon: $predicatAddCarbon,
        );

        if (null !== $onlyCarbonProperty) {
            BoolService::isPropExist($carbon, $onlyCarbonProperty, throw: true);
            \array_walk($arrayOfCarbon, static fn(\DateTimeInterface &$c) => $c = $c->{$onlyCarbonProperty});
        }

        return $arrayOfCarbon;
    }

    private static function getArrayOfCarbonCallback(CarbonImmutable $carbon, bool $includePassed, callable|bool $predicatAddCarbon, callable|string $carbonGetNext, Carbon|CarbonImmutable $carbonStart, Carbon|CarbonImmutable $carbonEnd): array
    {

        if (!\is_callable($carbonGetNext)) {
            BoolService::isPropExist($carbon, $carbonGetNext, throw: true);

            $carbonGetNext = static function (Carbon|CarbonImmutable $c) use ($carbonGetNext): Carbon|CarbonImmutable {
                return $c->copy()->add(1, $carbonGetNext);
            };
        }


        $carbons = [];

        $carbonAcc = $carbonStart;

        while ($carbonAcc->lte($carbonEnd)) {
            if (true === $includePassed || $carbonAcc->gte($carbon)) {
                if (\is_callable($predicatAddCarbon)) {
                    $isAddCarbon = $predicatAddCarbon($carbonAcc);
                } else {
                    $isAddCarbon = $predicatAddCarbon;
                }

                if (true === $isAddCarbon) {
                    $carbons[] = $carbonAcc->copy();
                }
            }
            $carbonAcc = $carbonGetNext($carbonAcc);
        }

        return $carbons;
    }

    private static function getEnsuredCarbonInstance(?\DateTimeInterface $carbon, bool $isImmutable = true): Carbon|CarbonImmutable
    {
        $carbon ??= static::getNow(isImmutable: $isImmutable);
        if (!$carbon instanceof Carbon || !$carbon instanceof CarbonImmutable) {
            if (true === $isImmutable) {
                $carbon = new CarbonImmutable($carbon);
            } else {
                $carbon = new Carbon($carbon);
            }
        }
        return $carbon;
    }
}
