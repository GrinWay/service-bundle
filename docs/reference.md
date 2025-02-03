Reference
------

| Service ID                   | Description                                                                           |
|------------------------------|---------------------------------------------------------------------------------------|
| [grinway_service.currency]() | Make operations with money, conversions<br>RUB -> EUR for instance<br>(see [tests]()) |

| Class name                | Description                                                                                                                      |
|---------------------------|----------------------------------------------------------------------------------------------------------------------------------|
| [FiguresRepresentation]() | If you deal dot-less numbers like `100` but actually it's `1.00` you have to use this class of static methods<br>(see [tests]()) |

| Symfony constraint class name | Valid when                                                                                               |
|-------------------------------|----------------------------------------------------------------------------------------------------------|
| [AbsolutePath]()              | `C:/` `/path`<br>(see [tests]())                                                                         |
| [LikeInt]()                   | `100` `001` `123`<br>(even `1.0` but only when it natively converts to string as `1`)<br>(see [tests]()) |
| [LikeNumeric]()               | `100` `1.2` `0.1`<br>(see [tests]())                                                                     |

| Symfony compiler pass      | Description                                                                                                                                                                                                                                                                                                                                                                                                                                                             |
|----------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [TagServiceLocatorsPass]() | If you have a tag, it means that you probably have a collection of `services`, this Compiler Pass gives you a possibility to get a certain Symfony `ServiceLocator` for each `service`. `Service` just have to have a particular information about tag part of related other its services. It means that you have a collection of other services, that related to the owner `service`, and each `service` has its own collection of related services<br>(see [tests]()) |
| [HideServiceByTagPass]()   | Makes service name with `.` in the beginning of service id by `tag name`<br>(see [tests]())                                                                                                                                                                                                                                                                                                                                                                             |

#### TagServiceLocatorsPass RESTRICTION

When you got `ServiceLocator` services with `TagServiceLocatorsPass` they can't use "methodCalls".
<br>
It means you won't be able to use `#[Required]` symfony attribute
<br>
If you do you will get an error:
> PhpDumper: Symfony\Component\DependencyInjection\Exception\RuntimeException: Cannot dump definitions which have method

| Symfony cache pool                      | Description                                                                                                         |
|-----------------------------------------|---------------------------------------------------------------------------------------------------------------------|
| [grinway_service.cache_pool.currency]() | Cache pool for currencies (default lifetime is `1 day`, but you can configure this value with bundle configuration) |                                                           |

| Symfony framework http client             | Description                                                                                                |
|-------------------------------------------|------------------------------------------------------------------------------------------------------------|
| [grinway_service.currency.fixer_latest]() | The http client with pre-set `base_uri` and GET query parameter `access_token` essential for [Fixer API]() |
