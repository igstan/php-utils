<?php

require 'NumeralBuilder.php';

class NumeralBuilderTest extends PHPUnit_Framework_TestCase
{
    public function testZero()
    {
        $result = NumeralBuilder()->toNumeral(0);
        $this->assertEquals('ZERO', $result);
    }

    public function testOne1()
    {
        $result = NumeralBuilder()->toNumeral(1);
        $this->assertEquals('UN', $result);
    }

    public function testOne2()
    {
        $result = NumeralBuilder()->toNumeral('01');
        $this->assertEquals('UN', $result);
    }

    public function testTen()
    {
        $result = NumeralBuilder()->toNumeral('10');
        $this->assertEquals('ZECE', $result);
    }


    public function testSaisprezece()
    {
        $result = NumeralBuilder()->toNumeral('16');
        $this->assertEquals('SAISPREZECE', $result);
    }

    public function testSaptesprezece()
    {
        $result = NumeralBuilder()->toNumeral('17');
        $this->assertEquals('SAPTESPREZECE', $result);
    }

    public function testDecimalToNumeral2()
    {
        $result = NumeralBuilder()->toNumeral('40');
        $this->assertEquals('PATRUZECI', $result);
    }

    public function testDecimalToNumeral3()
    {
        $result = NumeralBuilder()->toNumeral('68');
        $this->assertEquals('SAIZECI SI OPT', $result);
    }

    public function testHundreds1()
    {
        $result = NumeralBuilder()->toNumeral('100');
        $this->assertEquals('O SUTA', $result);
    }

    public function testHundreds2()
    {
        $result = NumeralBuilder()->toNumeral('200');
        $this->assertEquals('DOUA SUTE', $result);
    }

    public function testHundreds3()
    {
        $result = NumeralBuilder()->toNumeral('131');
        $this->assertEquals('O SUTA TREIZECI SI UNU', $result);
    }

    public function testCombinationOfHundredsAndUnits()
    {
        $result = NumeralBuilder()->toNumeral('201');
        $this->assertEquals('DOUA SUTE UNU', $result);
    }

    public function testCombinationOfHundredsDecimalsAndUnits1()
    {
        $result = NumeralBuilder()->toNumeral('241');
        $this->assertEquals('DOUA SUTE PATRUZECI SI UNU', $result);
    }

    public function testCombinationOfHundredsDecimalsAndUnits2()
    {
        $result = NumeralBuilder()->toNumeral('416');
        $this->assertEquals('PATRU SUTE SAISPREZECE', $result);
    }

    public function testThousandsSingular()
    {
        $result = NumeralBuilder()->toNumeral('1000');
        $this->assertEquals('O MIE', $result);
    }

    public function testThousandsPlural()
    {
        $result = NumeralBuilder()->toNumeral('2000');
        $this->assertEquals('DOUA MII', $result);
    }

    public function testCombinationOfThousandsAndUnits()
    {
        $result = NumeralBuilder()->toNumeral('2001');
        $this->assertEquals('DOUA MII UNU', $result);
    }

    public function testCombinationOfThousandsHundredsAndUnits()
    {
        $result = NumeralBuilder()->toNumeral('2401');
        $this->assertEquals('DOUA MII PATRU SUTE UNU', $result);
    }

    public function testCombinationOfThousandsHundredsDecimalsAndUnits()
    {
        $result = NumeralBuilder()->toNumeral('3571');
        $this->assertEquals('TREI MII CINCI SUTE SAPTEZECI SI UNU', $result);
    }

    public function testTensOfThousands()
    {
        $result = NumeralBuilder()->toNumeral('10000');
        $this->assertEquals('ZECE MII', $result);
    }

    public function testHundredOfThousands()
    {
        $result = NumeralBuilder()->toNumeral('131001');
        $this->assertEquals('O SUTA TREIZECI SI UNU DE MII, UNU', $result);
    }

    public function testComplicatedNumber()
    {
        $result = NumeralBuilder()->toNumeral('367621');
        $this->assertEquals('TREI SUTE SAIZECI SI SAPTE DE MII, SASE SUTE DOUAZECI SI UNU', $result);
    }

    public function testOneHundredThousands()
    {
        $result = NumeralBuilder()->toNumeral('100000');
        $this->assertEquals('O SUTA DE MII', $result);
    }

    public function testOneHundredMillions()
    {
        $result = NumeralBuilder()->toNumeral('100000000');
        $this->assertEquals('O SUTA DE MILIOANE', $result);
    }

    public function test100100000()
    {
        $result = NumeralBuilder()->toNumeral('100100000');
        $this->assertEquals('O SUTA DE MILIOANE, O SUTA DE MII', $result);
    }

    public function test999999()
    {
        $result = NumeralBuilder()->toNumeral('999999');
        $this->assertEquals('NOUA SUTE NOUAZECI SI NOUA DE MII, NOUA SUTE NOUAZECI SI NOUA', $result);
    }

    public function testOneMillion()
    {
        $result = NumeralBuilder()->toNumeral('1000000');
        $this->assertEquals('UN MILION', $result);
    }

    public function test999999999()
    {
        $result = NumeralBuilder()->toNumeral('999999999');
        $this->assertEquals('NOUA SUTE NOUAZECI SI NOUA DE MILIOANE, NOUA SUTE NOUAZECI SI NOUA DE MII, NOUA SUTE NOUAZECI SI NOUA', $result);
    }

    public function test111111111()
    {
        $result = NumeralBuilder()->toNumeral('111111111');
        $this->assertEquals('O SUTA UNSPREZECE MILIOANE, O SUTA UNSPREZECE MII, O SUTA UNSPREZECE', $result);
    }

    public function test123456789()
    {
        $result = NumeralBuilder()->toNumeral('123456789');
        $this->assertEquals('O SUTA DOUAZECI SI TREI DE MILIOANE, PATRU SUTE CINCIZECI SI SASE DE MII, SAPTE SUTE OPTZECI SI NOUA', $result);
    }
}
