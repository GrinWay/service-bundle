<?php

namespace GrinWay\Service\Tests\Unit\Transfer;

use GrinWay\Service\Service\Currency;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Currency::class)]
class RubTransferAmountTest extends AbstractCurrencyValidTransferTestCase
{
    public static function fromCurrency(): string
    {
        return 'RUB';
    }

    public static function amount(): string
    {
        return '101';
    }

    public static function validForStaticPayloadTransferredAmounts(): \Generator
    {
        yield '003';
        yield '077';
        yield '098';
        yield '407';
        yield '001';
        yield '467';
        yield '1076';
        yield '001';
        yield '001';
        yield '001';
        yield '001';
        yield '002';
        yield '124';
        yield '001';
        yield '000';
        yield '3031';
        yield '001';
        yield '001';
        yield '007';
        yield '005';
        yield '001';
        yield '000';
        yield '088';
        yield '014';
        yield '003';
        yield '20083';
        yield '002';
        yield '001';
        yield '2923';
        yield '000';
        yield '000';
        yield '1007';
        yield '007';
        yield '007';
        yield '4308';
        yield '516';
        yield '001';
        yield '027';
        yield '108';
        yield '024';
        yield '182';
        yield '007';
        yield '063';
        yield '138';
        yield '051';
        yield '015';
        yield '131';
        yield '000';
        yield '002';
        yield '000';
        yield '000';
        yield '002';
        yield '000';
        yield '015';
        yield '000';
        yield '074';
        yield '8853';
        yield '007';
        yield '214';
        yield '007';
        yield '026';
        yield '007';
        yield '133';
        yield '403';
        yield '16758';
        yield '003';
        yield '000';
        yield '088';
        yield '1341';
        yield '43138';
        yield '145';
        yield '000';
        yield '161';
        yield '000';
        yield '159';
        yield '132';
        yield '089';
        yield '4121';
        yield '484';
        yield '922';
        yield '1493';
        yield '000';
        yield '000';
        yield '530';
        yield '22284';
        yield '91724';
        yield '305';
        yield '203';
        yield '019';
        yield '003';
        yield '000';
        yield '005';
        yield '010';
        yield '019';
        yield '4763';
        yield '060';
        yield '3328';
        yield '3481';
        yield '008';
        yield '041';
        yield '047';
        yield '015';
        yield '1776';
        yield '021';
        yield '004';
        yield '065';
        yield '019';
        yield '1540';
        yield '037';
        yield '011';
        yield '141';
        yield '001';
        yield '000';
        yield '001';
        yield '003';
        yield '004';
        yield '059';
        yield '285';
        yield '004';
        yield '8079';
        yield '003';
        yield '004';
        yield '115';
        yield '101';
        yield '1453';
        yield '003';
        yield '008';
        yield '015';
        yield '615';
        yield '011';
        yield '001';
        yield '000';
        yield '023';
        yield '21486';
        yield '585';
        yield '035';
        yield '21208';
        yield '008';
        yield '13322';
        yield '019';
        yield '034';
        yield '011';
        yield '003';
        yield '003';
        yield '002';
        yield '036';
        yield '006';
        yield '033';
        yield '2612';
        yield '042';
        yield '3771';
        yield '001';
        yield '044';
        yield '13290';
        yield '059';
        yield '25698';
        yield '121';
        yield '002';
        yield '647';
        yield '000';
        yield '000';
        yield '002';
        yield '000';
        yield '647';
        yield '118';
        yield '255';
        yield '019';
        yield '9223';
        yield '028';
        yield '329';
    }
}
