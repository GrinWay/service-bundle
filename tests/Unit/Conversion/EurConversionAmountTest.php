<?php

namespace GrinWay\Service\Tests\Unit\Conversion;

use GrinWay\Service\Service\Currency;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Currency::class)]
class EurConversionAmountTest extends AbstractCurrencyValidConversionTestCase
{
    public static function fromCurrency(): string
    {
        return 'EUR';
    }

    public static function amount(): string
    {
        return '9999';
    }

    public static function validForStaticPayloadTransferredAmounts(): \Generator
    {
        yield '38055';
        yield '782304';
        yield '994910';
        yield '4116993';
        yield '18666';
        yield '4724673';
        yield '10888852';
        yield '16612';
        yield '18675';
        yield '17655';
        yield '19523';
        yield '20912';
        yield '1263004';
        yield '19545';
        yield '3906';
        yield '30658308';
        yield '10361';
        yield '14055';
        yield '71571';
        yield '60530';
        yield '10357';
        yield '000';
        yield '896688';
        yield '144260';
        yield '33894';
        yield '203077887';
        yield '20804';
        yield '15065';
        yield '29560269';
        yield '9437';
        yield '369';
        yield '10188297';
        yield '74463';
        yield '75848';
        yield '43568495';
        yield '5224604';
        yield '10361';
        yield '274569';
        yield '1100659';
        yield '251985';
        yield '1841381';
        yield '74621';
        yield '639858';
        yield '1401759';
        yield '520410';
        yield '155416';
        yield '1326729';
        yield '9999';
        yield '24068';
        yield '8533';
        yield '8355';
        yield '29636';
        yield '8533';
        yield '158465';
        yield '8533';
        yield '751220';
        yield '89527267';
        yield '80117';
        yield '2166903';
        yield '80723';
        yield '263849';
        yield '76460';
        yield '1354783';
        yield '4077621';
        yield '169458653';
        yield '37029';
        yield '8533';
        yield '898281';
        yield '13567796';
        yield '436203016';
        yield '1466723';
        yield '8533';
        yield '1633430';
        yield '7348';
        yield '1608097';
        yield '1338695';
        yield '906083';
        yield '41675052';
        yield '4899258';
        yield '9325006';
        yield '15104232';
        yield '3196';
        yield '8631';
        yield '5366844';
        yield '225334760';
        yield '927492009';
        yield '3086593';
        yield '2061093';
        yield '193328';
        yield '30593';
        yield '6267';
        yield '50847';
        yield '103965';
        yield '193372';
        yield '48163383';
        yield '615167';
        yield '33652502';
        yield '35207075';
        yield '83139';
        yield '414919';
        yield '483350';
        yield '159668';
        yield '17959701';
        yield '214299';
        yield '46159';
        yield '662182';
        yield '193328';
        yield '15572761';
        yield '381120';
        yield '117284';
        yield '1434692';
        yield '18380';
        yield '3990';
        yield '10357';
        yield '38530';
        yield '42173';
        yield '605307';
        yield '2888937';
        yield '42135';
        yield '81694264';
        yield '37754';
        yield '49747';
        yield '1171127';
        yield '1021283';
        yield '14701560';
        yield '38861';
        yield '87589';
        yield '155570';
        yield '6227034';
        yield '114975';
        yield '14063';
        yield '8533';
        yield '237010';
        yield '217267435';
        yield '5919354';
        yield '363670';
        yield '214454196';
        yield '90625';
        yield '134715239';
        yield '193205';
        yield '350105';
        yield '113256';
        yield '36367';
        yield '33080';
        yield '24266';
        yield '369736';
        yield '70253';
        yield '341347';
        yield '26420851';
        yield '431943';
        yield '38131975';
        yield '10361';
        yield '448200';
        yield '134390259';
        yield '604784';
        yield '259856806';
        yield '1230092';
        yield '29019';
        yield '6547794';
        yield '330';
        yield '003';
        yield '28001';
        yield '7917';
        yield '6547668';
        yield '1193198';
        yield '2578623';
        yield '193451';
        yield '93262520';
        yield '289748';
        yield '3336275';
    }
}
