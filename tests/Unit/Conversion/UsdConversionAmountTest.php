<?php

namespace GrinWay\Service\Tests\Unit\Conversion;

use GrinWay\Service\Service\Currency;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Currency::class)]
class UsdConversionAmountTest extends AbstractCurrencyValidConversionTestCase
{
    public static function fromCurrency(): string
    {
        return 'USD';
    }

    public static function amount(): string
    {
        return '1111';
    }

    public static function validForStaticPayloadTransferredAmounts(): \Generator
    {
        yield '4080';
        yield '83884';
        yield '106682';
        yield '441456';
        yield '2001';
        yield '506616';
        yield '1167588';
        yield '1781';
        yield '2002';
        yield '1893';
        yield '2093';
        yield '2242';
        yield '135429';
        yield '2095';
        yield '418';
        yield '3287424';
        yield '1111';
        yield '1507';
        yield '7674';
        yield '6490';
        yield '1110';
        yield '000';
        yield '96150';
        yield '15468';
        yield '3634';
        yield '21775606';
        yield '2230';
        yield '1615';
        yield '3169684';
        yield '1011';
        yield '039';
        yield '1092469';
        yield '7984';
        yield '8133';
        yield '4671756';
        yield '560223';
        yield '1111';
        yield '29441';
        yield '118021';
        yield '27019';
        yield '197447';
        yield '8001';
        yield '68610';
        yield '150307';
        yield '55802';
        yield '16665';
        yield '142262';
        yield '1072';
        yield '2580';
        yield '915';
        yield '895';
        yield '3177';
        yield '915';
        yield '16991';
        yield '915';
        yield '80551';
        yield '9599816';
        yield '8590';
        yield '232352';
        yield '8655';
        yield '28291';
        yield '8198';
        yield '145270';
        yield '437234';
        yield '18170687';
        yield '3970';
        yield '915';
        yield '96320';
        yield '1454845';
        yield '46773113';
        yield '157273';
        yield '915';
        yield '175149';
        yield '787';
        yield '172432';
        yield '143545';
        yield '97157';
        yield '4468726';
        yield '525336';
        yield '999900';
        yield '1619594';
        yield '342';
        yield '925';
        yield '575475';
        yield '24162162';
        yield '99452978';
        yield '330968';
        yield '221006';
        yield '20730';
        yield '3280';
        yield '672';
        yield '5452';
        yield '11148';
        yield '20734';
        yield '5164456';
        yield '65963';
        yield '3608485';
        yield '3775179';
        yield '8914';
        yield '44490';
        yield '51828';
        yield '17120';
        yield '1925780';
        yield '22978';
        yield '4949';
        yield '71004';
        yield '20730';
        yield '1669833';
        yield '40866';
        yield '12576';
        yield '153838';
        yield '1970';
        yield '427';
        yield '1110';
        yield '4131';
        yield '4522';
        yield '64905';
        yield '309774';
        yield '4518';
        yield '8759900';
        yield '4048';
        yield '5334';
        yield '125577';
        yield '109510';
        yield '1576416';
        yield '4167';
        yield '9392';
        yield '16681';
        yield '667711';
        yield '12328';
        yield '1507';
        yield '915';
        yield '25414';
        yield '23297120';
        yield '634719';
        yield '38995';
        yield '22995463';
        yield '9717';
        yield '14445226';
        yield '20716';
        yield '37541';
        yield '12144';
        yield '3899';
        yield '3547';
        yield '2602';
        yield '39646';
        yield '7533';
        yield '36601';
        yield '2833051';
        yield '46316';
        yield '4088809';
        yield '1111';
        yield '48059';
        yield '14410379';
        yield '64849';
        yield '27863887';
        yield '131900';
        yield '3111';
        yield '702105';
        yield '035';
        yield '000';
        yield '3002';
        yield '848';
        yield '702092';
        yield '127944';
        yield '276500';
        yield '20743';
        yield '10000339';
        yield '31069';
        yield '357741';
    }
}
