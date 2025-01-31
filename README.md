GrinWay/service-bundle
======

About
------
It's the set of commonly used tools for a Symfony application.

Installation
------

```console
composer require grinway/service-bundle
```

Usage
------

###

Reference
------

### Ready to use `Compiler Pass`

| `Compiler Pass`                                                                                                   |                                                                                                                                                                                                                      Description                                                                                                                                                                                                                      |
|-------------------------------------------------------------------------------------------------------------------|:-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------:|
| [HideServiceByTagPass](https://github.com/GrinWay/service-bundle/blob/main/src/Pass/HideServiceByTagPass.php)     |                                                                                                                                                                                       Makes service name with `.` in the beginning of service id by `tag name`.                                                                                                                                                                                       |
| [TagServiceLocatorsPass](https://github.com/GrinWay/service-bundle/blob/main/src/Pass/TagServiceLocatorsPass.php) | If you have a tag, it means that you probably have a collection of `services`, this Compiler Pass gives you a possibility to get a certain Symfony `ServiceLocator` for each `service`. `Service` just have to have a particular information about tag part of related other its services. It means that you have a collection of other services, that related to the owner `service`, and each `service` has its own collection of related services. |

#### TagServiceLocatorsPass RESTRICTION
When you got `ServiceLocator` services with TagServiceLocatorsPass they can't use "methodCalls".
<br>
It means you won't be able to use Symfony #[Required] Attribute
<br>
If you do you will get an error:
> PhpDumper: Symfony\Component\DependencyInjection\Exception\RuntimeException: Cannot dump definitions which have method

### Ready to use `Services`

| `Service ID` (Definition)                                                                                                       |                                          Class                                           |                      Description                      |
|---------------------------------------------------------------------------------------------------------------------------------|:----------------------------------------------------------------------------------------:|:-----------------------------------------------------:|
| [grinway_service.currency](https://github.com/GrinWay/service-bundle/blob/main/config/service_hierarchy/services/Currency.yaml) | [Currency](https://github.com/GrinWay/service-bundle/blob/main/src/Service/Currency.php) | Make operations with money, conversions, and so on... |

Advanced
------
