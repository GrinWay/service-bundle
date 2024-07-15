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
}
