<?php

require 'NumeralBuilder.php';

class NumeralBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var NumeralBuilder
     */
    private $builder;

    public function setUp()
    {
        $this->builder = NumeralBuilder();
    }

    public function testZero()
    {
        $result = $this->builder->toNumeral(0);
        $this->assertEquals('ZERO', $result);
    }

    public function testOne1()
    {
        $result = $this->builder->toNumeral(1);
        $this->assertEquals('UN', $result);
    }

    public function testOne2()
    {
        $result = $this->builder->toNumeral('01');
        $this->assertEquals('UN', $result);
    }

    public function testTen()
    {
        $result = $this->builder->toNumeral('10');
        $this->assertEquals('ZECE', $result);
    }


    public function testSaisprezece()
    {
        $result = $this->builder->toNumeral('16');
        $this->assertEquals('SAISPREZECE', $result);
    }

    public function testSaptesprezece()
    {
        $result = $this->builder->toNumeral('17');
        $this->assertEquals('SAPTESPREZECE', $result);
    }

    public function testDecimalToNumeral2()
    {
        $result = $this->builder->toNumeral('40');
        $this->assertEquals('PATRUZECI', $result);
    }

    public function testDecimalToNumeral3()
    {
        $result = $this->builder->toNumeral('68');
        $this->assertEquals('SAIZECI SI OPT', $result);
    }

    public function testHundreds1()
    {
        $result = $this->builder->toNumeral('100');
        $this->assertEquals('O SUTA', $result);
    }

    public function testHundreds2()
    {
        $result = $this->builder->toNumeral('200');
        $this->assertEquals('DOUA SUTE', $result);
    }

    public function testHundreds3()
    {
        $result = $this->builder->toNumeral('131');
        $this->assertEquals('O SUTA TREIZECI SI UNU', $result);
    }

    public function testCombinationOfHundredsAndUnits()
    {
        $result = $this->builder->toNumeral('201');
        $this->assertEquals('DOUA SUTE UNU', $result);
    }

    public function testCombinationOfHundredsDecimalsAndUnits1()
    {
        $result = $this->builder->toNumeral('241');
        $this->assertEquals('DOUA SUTE PATRUZECI SI UNU', $result);
    }

    public function testCombinationOfHundredsDecimalsAndUnits2()
    {
        $result = $this->builder->toNumeral('416');
        $this->assertEquals('PATRU SUTE SAISPREZECE', $result);
    }

    public function testThousandsSingular()
    {
        $result = $this->builder->toNumeral('1000');
        $this->assertEquals('O MIE', $result);
    }

    public function testThousandsPlural()
    {
        $result = $this->builder->toNumeral('2000');
        $this->assertEquals('DOUA MII', $result);
    }

    public function testCombinationOfThousandsAndUnits()
    {
        $result = $this->builder->toNumeral('2001');
        $this->assertEquals('DOUA MII UNU', $result);
    }

    public function testCombinationOfThousandsHundredsAndUnits()
    {
        $result = $this->builder->toNumeral('2401');
        $this->assertEquals('DOUA MII PATRU SUTE UNU', $result);
    }

    public function testCombinationOfThousandsHundredsDecimalsAndUnits()
    {
        $result = $this->builder->toNumeral('3571');
        $this->assertEquals('TREI MII CINCI SUTE SAPTEZECI SI UNU', $result);
    }

    public function testTensOfThousands()
    {
        $result = $this->builder->toNumeral('10000');
        $this->assertEquals('ZECE MII', $result);
    }

    public function testHundredOfThousands()
    {
        $result = $this->builder->toNumeral('131001');
        $this->assertEquals('O SUTA TREIZECI SI UNU DE MII, UNU', $result);
    }

    public function testComplicatedNumber()
    {
        $result = $this->builder->toNumeral('367621');
        $this->assertEquals('TREI SUTE SAIZECI SI SAPTE DE MII, SASE SUTE DOUAZECI SI UNU', $result);
    }

    public function testOneHundredThousands()
    {
        $result = $this->builder->toNumeral('100000');
        $this->assertEquals('O SUTA DE MII', $result);
    }

    public function testOneHundredMillions()
    {
        $result = $this->builder->toNumeral('100000000');
        $this->assertEquals('O SUTA DE MILIOANE', $result);
    }

    public function test100100000()
    {
        $result = $this->builder->toNumeral('100100000');
        $this->assertEquals('O SUTA DE MILIOANE, O SUTA DE MII', $result);
    }

    public function test999999()
    {
        $result = $this->builder->toNumeral('999999');
        $this->assertEquals('NOUA SUTE NOUAZECI SI NOUA DE MII, NOUA SUTE NOUAZECI SI NOUA', $result);
    }

    public function testOneMillion()
    {
        $result = $this->builder->toNumeral('1000000');
        $this->assertEquals('UN MILION', $result);
    }

    public function test999999999()
    {
        $result = $this->builder->toNumeral('999999999');
        $this->assertEquals('NOUA SUTE NOUAZECI SI NOUA DE MILIOANE, NOUA SUTE NOUAZECI SI NOUA DE MII, NOUA SUTE NOUAZECI SI NOUA', $result);
    }

    public function test111111111()
    {
        $result = $this->builder->toNumeral('111111111');
        $this->assertEquals('O SUTA UNSPREZECE MILIOANE, O SUTA UNSPREZECE MII, O SUTA UNSPREZECE', $result);
    }

    public function test123456789()
    {
        $result = $this->builder->toNumeral('123456789');
        $this->assertEquals('O SUTA DOUAZECI SI TREI DE MILIOANE, PATRU SUTE CINCIZECI SI SASE DE MII, SAPTE SUTE OPTZECI SI NOUA', $result);
    }
}
