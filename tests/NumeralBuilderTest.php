<?php

require 'NumeralBuilder.php';

class NumeralBuilderTest extends PHPUnit_Framework_TestCase
{
    public function testZero()
    {
        $result = NumeralBuilder()->convert(0);
        $this->assertEquals('ZERO', $result);
    }

    public function testOne1()
    {
        $result = NumeralBuilder()->convert(1);
        $this->assertEquals('UN', $result);
    }

    public function testOne2()
    {
        $result = NumeralBuilder()->convert('01');
        $this->assertEquals('UN', $result);
    }

    public function testTen()
    {
        $result = NumeralBuilder()->convert('10');
        $this->assertEquals('ZECE', $result);
    }


    public function testSaisprezece()
    {
        $result = NumeralBuilder()->convert('16');
        $this->assertEquals('SAISPREZECE', $result);
    }

    public function testSaptesprezece()
    {
        $result = NumeralBuilder()->convert('17');
        $this->assertEquals('SAPTESPREZECE', $result);
    }

    public function testDecimalToNumeral2()
    {
        $result = NumeralBuilder()->convert('40');
        $this->assertEquals('PATRUZECI', $result);
    }

    public function testDecimalToNumeral3()
    {
        $result = NumeralBuilder()->convert('68');
        $this->assertEquals('SAIZECI SI OPT', $result);
    }

    public function testHundreds1()
    {
        $result = NumeralBuilder()->convert('100');
        $this->assertEquals('O SUTA', $result);
    }

    public function testHundreds2()
    {
        $result = NumeralBuilder()->convert('200');
        $this->assertEquals('DOUA SUTE', $result);
    }

    public function testHundreds3()
    {
        $result = NumeralBuilder()->convert('131');
        $this->assertEquals('O SUTA TREIZECI SI UNU', $result);
    }

    public function testCombinationOfHundredsAndUnits()
    {
        $result = NumeralBuilder()->convert('201');
        $this->assertEquals('DOUA SUTE UNU', $result);
    }

    public function testCombinationOfHundredsDecimalsAndUnits1()
    {
        $result = NumeralBuilder()->convert('241');
        $this->assertEquals('DOUA SUTE PATRUZECI SI UNU', $result);
    }

    public function testCombinationOfHundredsDecimalsAndUnits2()
    {
        $result = NumeralBuilder()->convert('416');
        $this->assertEquals('PATRU SUTE SAISPREZECE', $result);
    }

    public function testThousandsSingular()
    {
        $result = NumeralBuilder()->convert('1000');
        $this->assertEquals('O MIE', $result);
    }

    public function testThousandsPlural()
    {
        $result = NumeralBuilder()->convert('2000');
        $this->assertEquals('DOUA MII', $result);
    }

    public function testCombinationOfThousandsAndUnits()
    {
        $result = NumeralBuilder()->convert('2001');
        $this->assertEquals('DOUA MII UNU', $result);
    }

    public function testCombinationOfThousandsHundredsAndUnits()
    {
        $result = NumeralBuilder()->convert('2401');
        $this->assertEquals('DOUA MII PATRU SUTE UNU', $result);
    }

    public function testCombinationOfThousandsHundredsDecimalsAndUnits()
    {
        $result = NumeralBuilder()->convert('3571');
        $this->assertEquals('TREI MII CINCI SUTE SAPTEZECI SI UNU', $result);
    }

    public function testTensOfThousands()
    {
        $result = NumeralBuilder()->convert('10000');
        $this->assertEquals('ZECE MII', $result);
    }

    public function testHundredOfThousands()
    {
        $result = NumeralBuilder()->convert('131001');
        $this->assertEquals('O SUTA TREIZECI SI UNU DE MII, UNU', $result);
    }

    public function testComplicatedNumber()
    {
        $result = NumeralBuilder()->convert('367621');
        $this->assertEquals('TREI SUTE SAIZECI SI SAPTE DE MII, SASE SUTE DOUAZECI SI UNU', $result);
    }

    public function testOneHundredThousands()
    {
        $result = NumeralBuilder()->convert('100000');
        $this->assertEquals('O SUTA DE MII', $result);
    }

    public function testOneHundredMillions()
    {
        $result = NumeralBuilder()->convert('100000000');
        $this->assertEquals('O SUTA DE MILIOANE', $result);
    }

    public function test100100000()
    {
        $result = NumeralBuilder()->convert('100100000');
        $this->assertEquals('O SUTA DE MILIOANE, O SUTA DE MII', $result);
    }

    public function test999999()
    {
        $result = NumeralBuilder()->convert('999999');
        $this->assertEquals('NOUA SUTE NOUAZECI SI NOUA DE MII, NOUA SUTE NOUAZECI SI NOUA', $result);
    }

    public function testOneMillion()
    {
        $result = NumeralBuilder()->convert('1000000');
        $this->assertEquals('UN MILION', $result);
    }

    public function test999999999()
    {
        $result = NumeralBuilder()->convert('999999999');
        $this->assertEquals('NOUA SUTE NOUAZECI SI NOUA DE MILIOANE, NOUA SUTE NOUAZECI SI NOUA DE MII, NOUA SUTE NOUAZECI SI NOUA', $result);
    }

    public function test111111111()
    {
        $result = NumeralBuilder()->convert('111111111');
        $this->assertEquals('O SUTA UNSPREZECE MILIOANE, O SUTA UNSPREZECE MII, O SUTA UNSPREZECE', $result);
    }

    public function test123456789()
    {
        $result = NumeralBuilder()->convert('123456789');
        $this->assertEquals('O SUTA DOUAZECI SI TREI DE MILIOANE, PATRU SUTE CINCIZECI SI SASE DE MII, SAPTE SUTE OPTZECI SI NOUA', $result);
    }
}
