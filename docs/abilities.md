Abilities
------
This bundle supplies you with:

* service [grinway_service.currency](https://github.com/GrinWay/service-bundle/blob/main/src/Service/Currency.php) (its [test]())
    * theoretically can throw [NotSuccessFixerException]() in `convertFromCurrencyToAnotherWithEndFigures`
      so you can catch it in your code
    * practically can throw [NoBaseFixerException]() in `convertFromCurrencyToAnotherWithEndFigures`
      so you can catch it in your code
* static helper class [FiguresRepresentation]() (its [test]())
* Symfony compiler passes:
    * [TagServiceLocatorsPass]() (its [test]())
    * [HideServiceByTagPass]() (its [test]())
* Symfony constraints:
    * [AbsolutePath]() (its [test]())
    * [LikeInt]() (its [test]())
    * [LikeNumeric]() (its [test]())
* Exceptions:
    * [NotSuccessFixerException]() (click and see when it throws)
    * [NoBaseFixerException]() (click and see when it throws)
