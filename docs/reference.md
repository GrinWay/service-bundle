Reference
------

### Services

| Service ID                                                                                               | Description                                                                                                                                                    |
|----------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [grinway_service.currency](https://github.com/GrinWay/service-bundle/blob/main/src/Service/Currency.php) | Make operations with money, conversions<br>RUB -> EUR for instance<br>(see [tests](https://github.com/GrinWay/service-bundle/tree/main/tests/Unit/Conversion)) |

### Classes

| Class name                                                                                                         | Description                                                                                                                                                                                                                  |
|--------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [FiguresRepresentation](https://github.com/GrinWay/service-bundle/blob/main/src/Service/FiguresRepresentation.php) | If you deal dot-less numbers like `100` but actually it's `1.00` you have to use this class of static methods<br>(see [tests](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/FiguresRepresentationTest.php)) |

### Symfony compiler pass

| Compiler class                                                                                                    | Description                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          |
|-------------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [TagServiceLocatorsPass](https://github.com/GrinWay/service-bundle/blob/main/src/Pass/TagServiceLocatorsPass.php) | If you have a tag, it means that you probably have a collection of `services`, this Compiler Pass gives you a possibility to get a certain Symfony `ServiceLocator` for each `service`. `Service` just have to have a particular information about tag part of related other its services. It means that you have a collection of other services, that related to the owner `service`, and each `service` has its own collection of related services<br>(see [tests](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/TagServiceLocatorsPassTest.php)) |
| [HideServiceByTagPass](https://github.com/GrinWay/service-bundle/blob/main/src/Pass/HideServiceByTagPass.php)     | Makes service name with `.` in the beginning of service id by `tag name`<br>(see [tests](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/HideServiceByTagPassTest.php))                                                                                                                                                                                                                                                                                                                                                                               |

#### TagServiceLocatorsPass RESTRICTION

When you got `ServiceLocator` services with `TagServiceLocatorsPass` they can't use "methodCalls".
<br>
It means you won't be able to use `#[Required]` symfony attribute
<br>
If you do you will get an error:
> PhpDumper: Symfony\Component\DependencyInjection\Exception\RuntimeException: Cannot dump definitions which have method

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

| Trait                                                                                                 | Description                |
|-------------------------------------------------------------------------------------------------------|----------------------------|
| [HasBufferTest](https://github.com/GrinWay/service-bundle/blob/main/src/Test/Trait/HasBufferTest.php) | Supplies buffer assertions |

### Exceptions

* [NotSuccessFixerException](https://github.com/GrinWay/service-bundle/blob/main/src/Exception/Fixer/NotSuccessFixerException.php)
* [NoBaseFixerException](https://github.com/GrinWay/service-bundle/blob/main/src/Exception/Fixer/NoBaseFixerException.php)

### DBAL Types

In your `%kernel.project_dir%/config/packages/doctrine.yaml`

```yaml
doctrine:
    dbal:
        
        mapping_types:
            percent: percent
            
        types:
            # Usage: #[ORM\Column(type: 'percent')]
            percent: 'GrinWay\Service\Doctrine\DBAL\Type\PercentType'
```
