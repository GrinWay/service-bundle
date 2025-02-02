transferAmountFromTo($endFiguresCount: -1)
transferAmountFromTo($endFiguresCount: 0)
transferAmountFromTo($endFiguresCount: 1)
transferAmountFromTo($endFiguresCount: 2)

concatNumbersWithCorrectCountOfEndFigures(empty start and end expect invalid argument exception)
end numer string 0000000000000000000000000000000000000000 with $endFiguresCount 2

//\dump($currencyService->transferAmountFromTo('100', 'RUB', 'RUB')); // OK

//\dump($currencyService->transferAmountFromTo('100', 'EUR', 'RUB')); // OK

//\dump($currencyService->transferAmountFromTo('100', 'EUR', 'RUB')); // OK
//\dump($currencyService->transferAmountFromTo('2211', 'EUR', 'RUB')); // OK
//\dump($currencyService->transferAmountFromTo('1500', 'EUR', 'RUB'));// OK
//\dump($currencyService->transferAmountFromTo('100', 'USD', 'RUB'));// OK
//\dump($currencyService->transferAmountFromTo('123', 'EUR', 'USD')); // OK

//\dump($currencyService->transferAmountFromTo('123', 'USD', 'EUR')); // OK
//\dump($currencyService->transferAmountFromTo('10000', 'RUB', 'EUR')); // OK


[Currency.php](..%2Fsrc%2FService%2FCurrency.php)

[FiguresRepresentation.php](..%2Fsrc%2FService%2FFiguresRepresentation.php)
