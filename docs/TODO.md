install `mysqldump` on ubuntu
https://serverfault.com/questions/1023046/install-mysqldump-for-mysql-8-0-on-ubuntu-18-04

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
                class: 'Carbon\Doctrine\DateTimeType'

            # to immediately get the CarbonImmutable when: 
            #
            # #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
            # protected $dateTime = null;
            datetime_immutable:
                class: 'Carbon\Doctrine\DateTimeImmutableType'
```

# To be able to test MySqlUtil
```yaml
when@test:
doctrine:
dbal:
url: '%env(resolve:GRINWAY_SERVICE_DATABASE_URL)%'
server_version: '8.0.40'
```

`Type`

`event listenere for createdAt updatedAt traits`
automatically createdAt and updatedAt is written!
tests for
CreatedAtEventListener
UpdatedAtEventListener

`CarbonUtil` class docs, test, real docs

`ActiveAndPriorityAwareRepositoryTrait`
`Active` `Priority`

`TEST StringService`

`CarbonUtil removed use DateTimeService instead`
`TEST DOCS DateTimeService`

`DoctrineUtil`


Avoid failure transport on Unrecoverable... as well as retry
`unrecoverable_do_not_send_messages_to_failure_transport_middleware`
```yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true
    unrecoverable_do_not_send_messages_to_failure_transport_middleware:
        class: 'GrinWay\Service\Messenger\Middleware\AvoidFailureTransportWhenUnrecoverableMessageHandlingExceptionIsThrownMiddleware'
        arguments:
            $logUnrecoverableException: true
            $includeTractInUnrecoverableExceptionLog: false

```
```yaml
framework:
    messenger:
        buses:
            command.bus:
                default_middleware:
                    enabled: true
                    allow_no_handlers: false
                    allow_no_senders: true
                middleware:
                - unrecoverable_do_not_send_messages_to_failure_transport_middleware
```

`[CarbonIntervalString.php](..%2Fsrc%2FValidator%2FCarbonIntervalString.php)`


`test for SessionAware`
