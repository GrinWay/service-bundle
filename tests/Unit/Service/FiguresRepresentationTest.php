<?php

namespace GrinWay\Service\Tests\Unit\Service;

use GrinWay\Service\Service\FiguresRepresentation;
use GrinWay\Service\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[CoversClass(FiguresRepresentation::class)]
class FiguresRepresentationTest extends AbstractUnitTestCase
{
    public function testGetStartEndNumbersWithEndFiguresWhenNumberCountLessThan10EndCountThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        FiguresRepresentation::getStartEndNumbersWithEndFigures(
            '1245',
            10,
        );
    }

    public function testConcatNumbersWithCorrectCountOfEndFiguresStringsThrowsWhenEndFloatLike()
    {
        $this->expectException(ValidationFailedException::class);
        FiguresRepresentation::concatStartEndPartsWithEndFigures(
            '01',
            '23.',
            2,
        );
    }

    public function testConcatNumbersWithCorrectCountOfEndFiguresStringsThrowsWhenStartFloatLike()
    {
        $this->expectException(ValidationFailedException::class);
        FiguresRepresentation::concatStartEndPartsWithEndFigures(
            '01.',
            '23',
            2,
        );
    }

    public function testConcatNumbersWithCorrectCountOfEndFiguresZeroEndCount()
    {
        $result = FiguresRepresentation::concatStartEndPartsWithEndFigures(
            123456,
            '00000023',
            0,
        );
        $this->assertSame('123456', $result);
    }

    public function testConcatNumbersWithCorrectCountOfEndFiguresThrowsOnNegativeIntEnd()
    {
        $this->expectException(ValidationFailedException::class);
        FiguresRepresentation::concatStartEndPartsWithEndFigures(
            0,
            -123456,
            0,
        );
    }

    public function testConcatNumbersWithCorrectCountOfEndFiguresThrowsOnNegativeIntStart()
    {
        $this->expectException(ValidationFailedException::class);
        FiguresRepresentation::concatStartEndPartsWithEndFigures(
            -123456,
            '00000023',
            0,
        );
    }

    public function testConcatNumbersWithCorrectCountOfEndFiguresThrowsOnNegativeStringEnd()
    {
        $this->expectException(ValidationFailedException::class);
        FiguresRepresentation::concatStartEndPartsWithEndFigures(
            0,
            '-123456',
            0,
        );
    }

    public function testConcatNumbersWithCorrectCountOfEndFiguresThrowsOnNegativeStringStart()
    {
        $this->expectException(ValidationFailedException::class);
        FiguresRepresentation::concatStartEndPartsWithEndFigures(
            '-123456',
            '00000023',
            0,
        );
    }

    public function testConcatNumbersWithCorrectCountOfEndFiguresWithMoreThanPhpIntMaxEndCount()
    {
        $result = FiguresRepresentation::concatStartEndPartsWithEndFigures(
            12,
            '20934820480218401284021834028401289038402983401234',
            50,
        );
        $this->assertSame('1220934820480218401284021834028401289038402983401234', $result);
    }

    public function testConcatNumbersWithCorrectCountOfEndFiguresWithEndPartLeadZeros()
    {
        $result = FiguresRepresentation::concatStartEndPartsWithEndFigures(
            1,
            '00000023',
            2,
        );
        $this->assertSame('100', $result);
    }

    public function testConcatNumbersWithCorrectCountOfEndFiguresIntsFillWithZerozIfEndCountMore()
    {
        $result = FiguresRepresentation::concatStartEndPartsWithEndFigures(
            1,
            23,
            3,
        );
        $this->assertSame('1230', $result);
    }

    public function testConcatNumbersWithCorrectCountOfEndFiguresStringsWithStartLeadZero()
    {
        $result = FiguresRepresentation::concatStartEndPartsWithEndFigures(
            '01',
            '23',
            2,
        );
        $this->assertSame('123', $result);
    }

    public function testConcatNumbersWithCorrectCountOfEndFiguresStrings()
    {
        $result = FiguresRepresentation::concatStartEndPartsWithEndFigures(
            '1',
            '23',
            2,
        );
        $this->assertSame('123', $result);
    }

    public function testConcatNumbersWithCorrectCountOfEndFiguresInts()
    {
        $result = FiguresRepresentation::concatStartEndPartsWithEndFigures(
            1,
            23,
            2,
        );
        $this->assertSame('123', $result);
    }

    public function testGetEndFiguresWithEndFiguresWith4()
    {
        $end = FiguresRepresentation::getEndFiguresWithEndFigures(
            '123456',
            4,
        );

        $this->assertSame('3456', $end);
    }

    public function testGetStartFiguresWithEndFiguresWith4()
    {
        $end = FiguresRepresentation::getStartFiguresWithEndFigures(
            '123456',
            4,
        );

        $this->assertSame('12', $end);
    }

    public function testGetEndNumberWithEndFiguresWithEqualActualNumberAndEndCountLength()
    {
        $end = FiguresRepresentation::getEndNumberWithEndFigures(
            '1',
            1,
        );

        $this->assertSame('1', $end);
    }

    public function testGetEndNumberWithEndFiguresWith3()
    {
        $end = FiguresRepresentation::getEndNumberWithEndFigures(
            '1234',
            3,
        );

        $this->assertSame('234', $end);
    }

    public function testGetStartNumberWithEndFiguresWithEqualActualNumberAndEndCountLength()
    {
        $start = FiguresRepresentation::getStartNumberWithEndFigures(
            '1',
            1,
        );

        $this->assertSame(0, $start);
    }

    public function testGetStartNumberWithEndFiguresWith1()
    {
        $start = FiguresRepresentation::getStartNumberWithEndFigures(
            '123',
            1,
        );

        $this->assertSame(12, $start);
    }

    public function testGetStartEndNumbersWithEndFiguresWithEqualLengthAndFiguresEndCount()
    {
        [$start, $end] = FiguresRepresentation::getStartEndNumbersWithEndFigures(
            '1234567890',
            10,
        );

        $this->assertSame(0, $start);
        $this->assertSame('1234567890', $end);
    }

    public function testGetStartEndNumbersWithEndFigures()
    {
        [$start, $end] = FiguresRepresentation::getStartEndNumbersWithEndFigures(
            '1245',
            2,
        );

        $this->assertSame(12, $start);
        $this->assertSame('45', $end);
    }

    public function testGetStartEndNumbersWithEndFiguresThrowsOnNegativeNumber()
    {
        $this->expectException(ValidationFailedException::class);
        FiguresRepresentation::getStartEndNumbersWithEndFigures(
            '-1245',
            2,
        );
    }

    public function testNumberWithEndFiguresAsFloat()
    {
        $float = FiguresRepresentation::numberWithEndFiguresAsFloat(
            '123456',
            2,
        );
        $this->assertSame(1234.56, $float);
    }

    public function testGetStringWithEndFiguresWithStringComma2()
    {
        $result = FiguresRepresentation::getStringWithEndFigures(
            '123,456789',
            2,
        );
        $this->assertSame('12345', $result);
    }

    public function testGetStringWithEndFiguresWithInt0()
    {
        $result = FiguresRepresentation::getStringWithEndFigures(
            123,
            0,
        );
        $this->assertSame('123', $result);
    }

    public function testGetStringWithEndFiguresWithInt10()
    {
        $result = FiguresRepresentation::getStringWithEndFigures(
            123,
            10,
        );
        $this->assertSame('1230000000000', $result);
    }

    public function testGetStringWithEndFiguresWithFloat10()
    {
        $result = FiguresRepresentation::getStringWithEndFigures(
            123.456789,
            10,
        );
        $this->assertSame('1234567890000', $result);
    }

    public function testGetStringWithEndFiguresWithFloat0()
    {
        $result = FiguresRepresentation::getStringWithEndFigures(
            123.456789,
            0,
        );
        $this->assertSame('123', $result);
    }

    public function testGetStringWithEndFiguresWithFloat2()
    {
        $result = FiguresRepresentation::getStringWithEndFigures(
            123.456789,
            2,
        );
        $this->assertSame('12345', $result);
    }

    public function testGetStringWithEndFiguresStringCount0()
    {
        $result = FiguresRepresentation::getStringWithEndFigures(
            '00000000000000123.456',
            0,
        );
        $this->assertSame('123', $result);
    }

    public function testGetStringWithEndFiguresStringCount1()
    {
        $result = FiguresRepresentation::getStringWithEndFigures(
            '00000000000000123.456',
            1,
        );
        $this->assertSame('1234', $result);
    }

    public function testGetStringWithEndFiguresStringCount2()
    {
        $result = FiguresRepresentation::getStringWithEndFigures(
            '00000000000000123.456',
            2,
        );
        $this->assertSame('12345', $result);
    }

    public function testGetStringWithEndFiguresStringCount3()
    {
        $result = FiguresRepresentation::getStringWithEndFigures(
            '00000000000000123.456',
            3,
        );
        $this->assertSame('123456', $result);
    }

    public function testGetStringWithEndFiguresStringCount10()
    {
        $result = FiguresRepresentation::getStringWithEndFigures(
            '00000000000000123.456789',
            10,
        );
        $this->assertSame('1234567890000', $result);
    }
}
