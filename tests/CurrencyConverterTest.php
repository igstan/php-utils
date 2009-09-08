<?php

require_once 'CurrencyConverter.php';

class CurrencyConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CurrencyConverter
     */
    private $converter;

    public function setUp()
    {
        $path            = dirname(__FILE__) . '/fixtures/rates.xml';
        $xmlSource       = file_get_contents($path);
        $this->converter = CurrencyConverter($xmlSource);
    }

    public function testEurToRonConversion()
    {
        $converted = $this->converter->convert(1, 'EUR', 'RON');
        $this->assertEquals(4.2175, $converted);
    }

    public function testEurToRonConversionUsingConstants()
    {
        $EUR       = CurrencyConverter::CURRENCY_EUR;
        $RON       = CurrencyConverter::CURRENCY_RON;
        $converted = $this->converter->convert(1, $EUR, $RON);

        $this->assertEquals(4.2175, $converted);
    }

    public function testConversionWithMagicCallMethod()
    {
        $converted = $this->converter->convertNzdToRon(1);
        $this->assertEquals(1.9125, $converted);
    }

    public function testConversionFromCurrenciesWithMultipliers1()
    {
        $converted = $this->converter->convertHufToRon(100);
        $this->assertEquals(1.5314, $converted);
    }

    public function testConversionFromCurrenciesWithMultipliers2()
    {
        $converted = $this->converter->convertHufToRon(50);
        $this->assertEquals(0.7657, $converted);
    }

    public function testConversionUsesAmount()
    {
        $converted = $this->converter->convertEurToRon(3);
        $this->assertEquals(12.6525, $converted);
    }
    
    /**
     * @expectedException RuntimeException
     */
    public function testInvalidCurrencyCodeThrowsException()
    {
        $converted = $this->converter->convertFooToRon(3);
    }

    public function testConversionToSameCurrencyDoesNotModifyAmmount()
    {
        $converted = $this->converter->convertRonToRon(3);
        $this->assertEquals(3, $converted);
    }

    /**
     * OrigCurrency is an element in the XML file supplied by BNR. It holds
     * the currency code RON.
     */
    public function testConversionToACurrencyWhichIsNotOrigCurrency()
    {
        $converted = $this->converter->convertEurToUsd(100);
        $this->assertEquals(139.7310, $converted);
    }

    public function testConversionToANonOrigCurrencyWhichHasMultiplier()
    {
        $converted = $this->converter->convertEurToHuf(100);
        $this->assertEquals(27540.1593, $converted);
    }

    public function testConversionToANonOrigCurrencyFromOneThatHasMultiplier()
    {
        $converted = $this->converter->convertHufToEur(10000);
        $this->assertEquals(36.3106, $converted);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetOriginlCurrencyThrowsException()
    {
        $path      = dirname(__FILE__) . '/fixtures/bad-rates.xml';
        $xmlSource = file_get_contents($path);
        $converter = CurrencyConverter($xmlSource);
        $converter->getOriginalCurrency();
    }
}
