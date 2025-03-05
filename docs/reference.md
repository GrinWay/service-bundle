Reference
------

### Services

| Service ID                                                                                               | Description                                                                                                                                                    |
|----------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [grinway_service.currency](https://github.com/GrinWay/service-bundle/blob/main/src/Service/Currency.php) | Make operations with money, conversions<br>RUB -> EUR for instance<br>(see [tests](https://github.com/GrinWay/service-bundle/tree/main/tests/Unit/Conversion)) |

> Since `1.4.3` if `access_token` of Fixer Api is invalid or maximum requests to the API reached
> algorithm will try to get last cached payload from non-removable filesystem cache!
> 
> It got more stable 🤩

### Classes

| Class name                                                                                                         | Description                                                                                                                                                                                                                  |
|--------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [FiguresRepresentation](https://github.com/GrinWay/service-bundle/blob/main/src/Service/FiguresRepresentation.php) | If you deal dot-less numbers like `100` but actually it's `1.00` you have to use this class of static methods<br>(see [tests](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/FiguresRepresentationTest.php)) |

### Symfony compiler pass

| Compiler class                                                                                                    | Description                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          |
|-------------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [TagServiceLocatorsPass](https://github.com/GrinWay/service-bundle/blob/main/src/Pass/TagServiceLocatorsPass.php) | If you have a tag, it means that you probably have a collection of `services`, this Compiler Pass gives you a possibility to get a certain Symfony `ServiceLocator` for each `service`. `Service` just have to have a particular information about tag part of related other its services. It means that you have a collection of other services, that related to the owner `service`, and each `service` has its own collection of related services<br>(see [tests](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/TagServiceLocatorsPassTest.php)) |
| [HideServiceByTagPass](https://github.com/GrinWay/service-bundle/blob/main/src/Pass/HideServiceByTagPass.php)     | Makes service name with `.` in the beginning of service id by `tag name`<br>(see [tests](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/HideServiceByTagPassTest.php))                                                                                                                                                                                                                                                                                                                                                                               |

### Symfony constraints

| Constraint class                                                                                   | Valid when                                                                                                                                                                                      |
|----------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [AbsolutePath](https://github.com/GrinWay/service-bundle/blob/main/src/Validator/AbsolutePath.php) | `C:/` `/path`<br>(see [tests](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/AbsolutePathValidatorTest.php))                                                                    |
| [LikeInt](https://github.com/GrinWay/service-bundle/blob/main/src/Validator/LikeInt.php)           | `100` `001` `123`<br>(even `1.0` but only when it natively converts to string as `1`)<br>(see [tests](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/LikeIntValidatorTest.php)) |
| [LikeNumeric](https://github.com/GrinWay/service-bundle/blob/main/src/Validator/LikeNumeric.php)   | `100` `1.2` `0.1`<br>(see [tests](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/LikeNumericValidatorTest.php))                                                                 |

### Symfony framework http clients

| Service ID                                                                                                                              | Service ID (autowiring version)                                                       | Description                                                                                                                 |
|-----------------------------------------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------|
| [grinway_service.currency.fixer_latest](https://github.com/GrinWay/service-bundle/blob/main/config/packages/framework_http_client.yaml) | `Symfony\Contracts\HttpClient\HttpClientInterface $grinwayServiceCurrencyFixerLatest` | The http client with pre-set `base_uri` and GET query parameter `access_token` essential for [Fixer API](https://fixer.io/) |

### Symfony cache pools

| Service ID                                                                                                                      | Description                                                                                                         |
|---------------------------------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------|
| [grinway_service.cache_pool.currency](https://github.com/GrinWay/service-bundle/blob/main/config/packages/framework_cache.yaml) | Cache pool for currencies (default lifetime is `1 day`, but you can configure this value with bundle configuration) |                                                           |

### Traits

Use the following traits in your test classes to obtain new functionality

| Trait                                                                                                 | Description                                               |
|-------------------------------------------------------------------------------------------------------|-----------------------------------------------------------|
| [HasBufferTest](https://github.com/GrinWay/service-bundle/blob/main/src/Test/Trait/HasBufferTest.php) | Supplies buffer assertions                                |
| [CreatedAt](https://github.com/GrinWay/service-bundle/blob/main/src/Trait/Doctrine/CreatedAt.php)     | Adds `\DateTimeImmutable $createdAt` field to your entity |
| [UpdatedAt](https://github.com/GrinWay/service-bundle/blob/main/src/Trait/Doctrine/UpdatedAt.php)     | Adds `\DateTimeImmutable $updatedAt` field to your entity |

### Doctrine Event Listeners

If you look at `%kernel.project_dir%/config/packages/grinway_service.yaml`

you'll find a part of enabling doctrine event listeners:

```yaml
grinway_service:
    doctrine:
        event_listeners:
            # Optional
            enabled: true

            # Optional
            auto_set_utc_date_time_before_to_database:
                enabled: false

            # Optional
            auto_set_created_at_when_pre_persist:
                enabled: true

            # Optional
            auto_set_updated_at_when_pre_update:
                enabled: true
```

| Doctrine Event Listener                                                                                                                                                     | Config key                                  | Description                                                                                                                                                                                                            |
|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------|---------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [DateTimeToUtcBeforeToDatabaseEventListener](https://github.com/GrinWay/service-bundle/blob/main/src/EventListener/Doctrine/DateTimeToUtcBeforeToDatabaseEventListener.php) | `auto_set_utc_date_time_before_to_database` | To be absolutely sure that all the `\DateTimeInterface` dates always were saved with `UTC` timezone (listens [onFlush](https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/reference/events.html#onflush))   |
| [CreatedAtEventListener](https://github.com/GrinWay/service-bundle/blob/main/src/EventListener/Doctrine/CreatedAtEventListener.php)                                         | `auto_set_created_at_when_pre_persist`      | Intended to work together with `CreatedAt` trait of this bundle to automatically set `createdAt` field on [prePersist](https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/reference/events.html#prepersist) |
| [UpdatedAtEventListener](https://github.com/GrinWay/service-bundle/blob/main/src/EventListener/Doctrine/UpdatedAtEventListener.php)                                         | `auto_set_updated_at_when_pre_update`       | Intended to work together with `UpdatedAt` trait of this bundle to automatically set `updatedAt` field on [preUpdate](https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/reference/events.html#preupdate)   |

> Set `enabled: false` to stop using these listeners
> (internally they will get removed from the Symfony service container)

### Exceptions

* [NotSuccessFixerException](https://github.com/GrinWay/service-bundle/blob/main/src/Exception/Fixer/NotSuccessFixerException.php)
* [NoBaseFixerException](https://github.com/GrinWay/service-bundle/blob/main/src/Exception/Fixer/NoBaseFixerException.php)

### DBAL Types

Add types in your `%kernel.project_dir%/config/packages/doctrine.yaml`

```yaml
doctrine:
    dbal:

        mapping_types:
            percent: percent
            week_day: week_day

        types:
            # Usage:
            # #[ORM\Column(type: PercentType::NAME)]
            percent: 'GrinWay\Service\Doctrine\DBAL\Type\PercentType'
            # Usage:
            # [ORM\Column(type: WeekDayType::NAME)]
            week_day: 'GrinWay\Service\Doctrine\DBAL\Type\WeekDayType'
```

to use them like:

```php
<?php

class Entity {

    #[ORM\Column(type: PercentType::NAME)]
    private ?Percent $percent = null,
    
    #[ORM\Column(type: WeekDayType::NAME)]
    private ?\Carbon\WeekDay $weekDay = null;
    
}
```
