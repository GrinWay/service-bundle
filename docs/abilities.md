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
* class with static methods only
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
* Traits:
    * [HasBufferTest](https://github.com/GrinWay/service-bundle/blob/main/src/Test/Trait/HasBufferTest.php)
* Exceptions:
    * [NotSuccessFixerException](https://github.com/GrinWay/service-bundle/blob/main/src/Exception/Fixer/NotSuccessFixerException.php)
      (click and see when it throws)
    * [NoBaseFixerException](https://github.com/GrinWay/service-bundle/blob/main/src/Exception/Fixer/NoBaseFixerException.php)
      (click and see when it throws)
