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
	*
	*/
	public static function getWeekends(?\DateTimeInterface $carbon = null, string $onlyCarbonProperty = null, bool $includePassed = true): array {
		$carbon = static::getEnsuredCarbonInstance($carbon, isImmutable: true);
		
		$arrayOfCarbonWeekends = static::getArrayOfCarbonWeekends($carbon, includePassed: $includePassed);
		
		if (null !== $onlyCarbonProperty) {
			if (isset($carbon->{$onlyCarbonProperty})) {
				\array_walk($arrayOfCarbonWeekends, static fn(\DateTimeInterface &$c) => $c = $c->{$onlyCarbonProperty});				
			} else {
				$mess = \sprintf(
					'The property name: "%s" does not exist in class: "%s"',
					$onlyCarbonProperty,
					\get_debug_type($carbon),
				);
				throw new \Exception($mess);
			}
		}
		
		return $arrayOfCarbonWeekends;
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
	
	private static function getArrayOfCarbonWeekends(CarbonImmutable $carbon, bool $includePassed): array {
		$carbons = [];
		
		$end = $carbon->endOfMonth();
		
		if (true === $includePassed) {
			$start = $carbon->startOfMonth();
		} else {
			$start = static::getNow();
		}
		
		while ($start->lte($end)) {
			if ($start->isWeekend()) {
				$carbons[] = $start->copy();					
			}
			$start = $start->addDay();
		}
		
		return $carbons;
	}
	
	private static function getEnsuredCarbonInstance(?\DateTimeInterface $carbon, bool $isImmutable = true): Carbon|CarbonImmutable {
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
