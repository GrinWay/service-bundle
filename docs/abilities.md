Abilities
------
This bundle supplies you with:

* service [grinway_service.currency](https://github.com/GrinWay/service-bundle/blob/main/src/Service/Currency.php)
  (its [test](https://github.com/GrinWay/service-bundle/tree/main/tests/Unit/Conversion))
    * `convertFromCurrencyToAnotherWithEndFigures`
      theoretically can throw the
      [NotSuccessFixerException](https://github.com/GrinWay/service-bundle/blob/main/src/Exception/Fixer/NotSuccessFixerException.php)
      so you can catch it in your code
    * `convertFromCurrencyToAnotherWithEndFigures`
      practically can throw the
      [NoBaseFixerException](https://github.com/GrinWay/service-bundle/blob/main/src/Exception/Fixer/NoBaseFixerException.php)
      so you can catch it in your code
*

service [grinway_service.string_service](https://github.com/GrinWay/service-bundle/blob/main/src/Service/StringService.php)
(its [test](https://github.com/GrinWay/service-bundle/tree/main/tests/Unit/StringService))

* `getPath`
* `getSafeAbsFilepath`
* `getSafePublicFilepath`

* Class with static methods only
  [FiguresRepresentation](https://github.com/GrinWay/service-bundle/blob/main/src/Service/FiguresRepresentation.php)
  (its
  [test](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/FiguresRepresentationTest.php))
* Symfony compiler passes:
    * [TagServiceLocatorsPass](https://github.com/GrinWay/service-bundle/blob/main/src/Pass/TagServiceLocatorsPass.php)
      (its
      [test](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/TagServiceLocatorsPassTest.php))
    * [HideServiceByTagPass](https://github.com/GrinWay/service-bundle/blob/main/src/Pass/HideServiceByTagPass.php)
      (its
      [test](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/HideServiceByTagPassTest.php))
* Symfony constraints:
    * [AbsolutePath](https://github.com/GrinWay/service-bundle/blob/main/src/Validator/AbsolutePath.php)
      (its
      [test](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/AbsolutePathValidatorTest.php))
    * [LikeInt](https://github.com/GrinWay/service-bundle/blob/main/src/Validator/LikeInt.php)
      (its
      [test](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/LikeIntValidatorTest.php))
    * [LikeNumeric](https://github.com/GrinWay/service-bundle/blob/main/src/Validator/LikeNumeric.php)
      (its
      [test](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/LikeNumericValidatorTest.php))
* Symfony framework http clients:
    * [grinway_service.currency.fixer_latest](https://github.com/GrinWay/service-bundle/blob/main/config/packages/framework_http_client.yaml)
* Symfony cache pools:
    * [grinway_service.cache_pool.currency](https://github.com/GrinWay/service-bundle/blob/main/config/packages/framework_cache.yaml)
* Test Traits:
    * [HasBufferTest](https://github.com/GrinWay/service-bundle/blob/main/src/Test/Trait/HasBufferTest.php)
    * [SessionAware](https://github.com/GrinWay/service-bundle/blob/main/src/Test/Trait/SessionAware.php)
* Doctrine Traits:
    * [CreatedAt](https://github.com/GrinWay/service-bundle/blob/main/src/Trait/Doctrine/CreatedAt.php)
      (its listener
      [CreatedAtEventListener](https://github.com/GrinWay/service-bundle/blob/main/src/EventListener/Doctrine/CreatedAtEventListener.php))
    * [UpdatedAt](https://github.com/GrinWay/service-bundle/blob/main/src/Trait/Doctrine/UpdatedAt.php)
      (its listener
      [UpdatedAtEventListener](https://github.com/GrinWay/service-bundle/blob/main/src/EventListener/Doctrine/UpdatedAtEventListener.php))
* Doctrine Functions (switch them on in the `doctrine.yaml`):
    * [LeaveOnlyNumbers](https://github.com/GrinWay/service-bundle/blob/main/src/Doctrine/Function/LeaveOnlyNumbers.php)
      (its
      [tests](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/Doctrine/Function/LeaveOnlyNumbersDoctrineFunctionTest.php))
    * [DateIntervalToSec](https://github.com/GrinWay/service-bundle/blob/main/src/Doctrine/Function/DateIntervalToSec.php)
      (its
      [tests](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/Doctrine/Function/DateIntervalToSecDoctrineFunctionTest.php))
* Doctrine Event Listeners:
    * [CreatedAtEventListener](https://github.com/GrinWay/service-bundle/blob/main/src/EventListener/Doctrine/CreatedAtEventListener.php)
      (its
      [tests](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/Doctrine/CreatedAtEventListenerTest.php))
    * [UpdatedAtEventListener](https://github.com/GrinWay/service-bundle/blob/main/src/EventListener/Doctrine/UpdatedAtEventListener.php)
      (its
      [tests](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/Doctrine/UpdatedAtEventListenerTest.php))
    * [DateTimeToUtcBeforeToDatabaseEventListener](https://github.com/GrinWay/service-bundle/blob/main/src/EventListener/Doctrine/DateTimeToUtcBeforeToDatabaseEventListener.php)
      (its
      [tests](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/Doctrine/DateTimeToUtcBeforeToDatabaseEventListenerTest.php))
* Exceptions:
    * [NotSuccessFixerException](https://github.com/GrinWay/service-bundle/blob/main/src/Exception/Fixer/NotSuccessFixerException.php)
      (click and see when it throws)
    * [NoBaseFixerException](https://github.com/GrinWay/service-bundle/blob/main/src/Exception/Fixer/NoBaseFixerException.php)
      (click and see when it throws)
* DBAL Types:
    * [Percent](https://github.com/GrinWay/service-bundle/blob/main/src/Doctrine/Type/Percent.php)
    * [WeekDayType](https://github.com/GrinWay/service-bundle/blob/main/src/Doctrine/DBAL/Type/WeekDayType.php)
* Classes to get extended:
    * [WorkerUtil](https://github.com/GrinWay/service-bundle/blob/main/src/Service/Messenger.php)
      (its
      [tests](https://github.com/GrinWay/service-bundle/blob/main/tests/Unit/WorkerUtil/WorkerUtilGetEntityAndItsPropertyPathRequiredDataThrowIfNotFoundTest.php))
