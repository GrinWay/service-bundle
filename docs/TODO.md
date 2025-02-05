new configuration

`grinway_service.carbon_factory_immutable`
$carbonFI->now()->isoFormat('LLLL'),
$carbonFI->now()->add(1, 'day')->isoFormat('LLLL'),
$carbonFI->make(Carbon::createFromTimestamp(\time()))->isoFormat('LLLL'),

`grinway_service.mysql_util`

`grinway_service.string_service`

tests for all new services

`Percent` DECIMAL(5, 2) Doctrine type
Usage:
#[ORM\Column(type: 'percent')]
private ?Percent $percent = null;

`CreatedAt`
`CreatedAt`

`pure tips`
```yaml
doctrine:
    dbal:
        types:
            
            # to immediately get the Carbon when:
            #
            # #[ORM\Column(type: Types::DATETIME)]
            # protected $dateTime = null;
            datetime:
                class: Carbon\Doctrine\DateTimeType

            # to immediately get the CarbonImmutable when: 
            #
            # #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
            # protected $dateTime = null;
            datetime_immutable:
                class: Carbon\Doctrine\DateTimeImmutableType
```
