<?php

namespace GrinWay\Service\Tests\Unit\Transfer;

use GrinWay\Service\Service\Currency;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Currency::class)]
class PhpTransferAmountTest extends AbstractCurrencyValidTransferTestCase
{
    public static function fromCurrency(): string
    {
        return 'PHP';
    }

    public static function amount(): string
    {
        return '10128';
    }

    public static function validForStaticPayloadTransferredAmounts(): \Generator
    {
        yield '636';
        yield '13089';
        yield '16646';
        yield '68885';
        yield '312';
        yield '79053';
        yield '182192';
        yield '277';
        yield '312';
        yield '295';
        yield '326';
        yield '349';
        yield '21132';
        yield '327';
        yield '065';
        yield '512974';
        yield '173';
        yield '235';
        yield '1197';
        yield '1012';
        yield '173';
        yield '000';
        yield '15003';
        yield '2413';
        yield '567';
        yield '3397899';
        yield '348';
        yield '252';
        yield '494602';
        yield '157';
        yield '006';
        yield '170470';
        yield '1245';
        yield '1269';
        yield '728988';
        yield '87418';
        yield '173';
        yield '4594';
        yield '18416';
        yield '4216';
        yield '30809';
        yield '1248';
        yield '10706';
        yield '23454';
        yield '8707';
        yield '2600';
        yield '22198';
        yield '167';
        yield '402';
        yield '142';
        yield '139';
        yield '495';
        yield '142';
        yield '2651';
        yield '142';
        yield '12569';
        yield '1497970';
        yield '1340';
        yield '36256';
        yield '1350';
        yield '4414';
        yield '1279';
        yield '22668';
        yield '68226';
        yield '2835382';
        yield '619';
        yield '142';
        yield '15030';
        yield '227016';
        yield '7298548';
        yield '24541';
        yield '142';
        yield '27330';
        yield '122';
        yield '26906';
        yield '22399';
        yield '15160';
        yield '697306';
        yield '81974';
        yield '156026';
        yield '252724';
        yield '053';
        yield '144';
        yield '89798';
        yield '3770301';
        yield '15518796';
        yield '51644';
        yield '34486';
        yield '3234';
        yield '511';
        yield '104';
        yield '850';
        yield '1739';
        yield '3235';
        yield '805869';
        yield '10292';
        yield '563073';
        yield '589084';
        yield '1391';
        yield '6942';
        yield '8087';
        yield '2671';
        yield '300501';
        yield '3585';
        yield '772';
        yield '11079';
        yield '3234';
        yield '260563';
        yield '6376';
        yield '1962';
        yield '24005';
        yield '307';
        yield '066';
        yield '173';
        yield '644';
        yield '705';
        yield '10128';
        yield '48337';
        yield '705';
        yield '1366908';
        yield '631';
        yield '832';
        yield '19595';
        yield '17088';
        yield '245986';
        yield '650';
        yield '1465';
        yield '2603';
        yield '104190';
        yield '1923';
        yield '235';
        yield '142';
        yield '3965';
        yield '3635318';
        yield '99042';
        yield '6084';
        yield '3588247';
        yield '1516';
        yield '2254055';
        yield '3232';
        yield '5857';
        yield '1895';
        yield '608';
        yield '553';
        yield '406';
        yield '6186';
        yield '1175';
        yield '5711';
        yield '442073';
        yield '7227';
        yield '638024';
        yield '173';
        yield '7499';
        yield '2248617';
        yield '10119';
        yield '4347924';
        yield '20581';
        yield '485';
        yield '109557';
        yield '005';
        yield '000';
        yield '468';
        yield '132';
        yield '109555';
        yield '19964';
        yield '43145';
        yield '3236';
        yield '1560468';
        yield '4848';
        yield '55822';
    }
}
