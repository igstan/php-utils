<?php

class CurrencyConverter
{
    /**
     * ISO 4217 codes for currencies supplied by BNR
     */
    const CURRENCY_AED = 'AED';
    const CURRENCY_AUD = 'AUD';
    const CURRENCY_BGN = 'BGN';
    const CURRENCY_BRL = 'BRL';
    const CURRENCY_CAD = 'CAD';
    const CURRENCY_CHF = 'CHF';
    const CURRENCY_CNY = 'CNY';
    const CURRENCY_CZK = 'CZK';
    const CURRENCY_DKK = 'DKK';
    const CURRENCY_EGP = 'EGP';
    const CURRENCY_EUR = 'EUR';
    const CURRENCY_GBP = 'GBP';
    const CURRENCY_HUF = 'HUF';
    const CURRENCY_INR = 'INR';
    const CURRENCY_JPY = 'JPY';
    const CURRENCY_KRW = 'KRW';
    const CURRENCY_MDL = 'MDL';
    const CURRENCY_MXN = 'MXN';
    const CURRENCY_NOK = 'NOK';
    const CURRENCY_NZD = 'NZD';
    const CURRENCY_PLN = 'PLN';
    const CURRENCY_RON = 'RON';
    const CURRENCY_RSD = 'RSD';
    const CURRENCY_RUB = 'RUB';
    const CURRENCY_SEK = 'SEK';
    const CURRENCY_TRY = 'TRY';
    const CURRENCY_UAH = 'UAH';
    const CURRENCY_USD = 'USD';
    const CURRENCY_XAU = 'XAU';
    const CURRENCY_XDR = 'XDR';
    const CURRENCY_ZAR = 'ZAR';

    /**
     * @var string
     */
    private $xmlSource;

    /**
     * @var DOMXPath
     */
    private $xpath;

    /**
     * @var string ISO currency code from OrigCurrency element
     */
    private $originalCurrency;

    /**
     * @param string   $xmlSource
     * @param callable $currencyExtractor
     */
    public function __construct($xmlSource)
    {
        $this->xmlSource = $xmlSource;
    }

    /**
     * @see    http://www.iso.org/iso/support/currency_codes_list-1.htm
     * @param  float  $amount
     * @param  string $fromCurrency ISO 4217 currency code
     * @param  string $toCurrency   ISO 4217 currency code
     * @return float  With precision 4 (four)
     */
    public function convert($amount, $fromCurrency, $toCurrency)
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        if (! $this->isOriginalCurrency($toCurrency)) {
            return $this->convertToNonOriginalCurrency($amount, $fromCurrency, $toCurrency);
        }

        return $this->convertToOriginalCurrency($amount, $fromCurrency);
    }

    /**
     * @param  float  $amount
     * @param  string $fromCurrency
     * @param  string $toCurrency
     * @return float
     */
    protected function convertToNonOriginalCurrency($amount, $fromCurrency, $toCurrency)
    {
        $toOrigCurrency = $this->convertToOriginalCurrency($amount, $fromCurrency);
        $toCurrencyRate = $this->getRate($toCurrency);

        return $this->toFixed($toOrigCurrency / $toCurrencyRate);
    }

    /**
     * @param  float  $amount
     * @param  string $fromCurrency
     * @return float
     */
    protected function convertToOriginalCurrency($amount, $fromCurrency)
    {
        $rateElement = $this->getRateElement($fromCurrency);
        $multiplier  = 1;

        if ($rateElement->hasAttribute('multiplier')) {
            $multiplier = $rateElement->getAttribute('multiplier');
        }

        $rate = $rateElement->textContent / $multiplier;

        return $this->toFixed($rate * $amount);
    }

    /**
     * @param  string $code ISO currency code
     * @return float
     */
    protected function getRate($code)
    {
        $rateElement = $this->getRateElement($code);
        $multiplier  = 1;

        if ($rateElement->hasAttribute('multiplier')) {
            $multiplier = $rateElement->getAttribute('multiplier');
        }

        $rate = $rateElement->textContent / $multiplier;

        return $rate;
    }

    /**
     * @param  float
     * @return float
     */
    protected function toFixed($amount)
    {
        return round($amount, 4);
    }

    /**
     * Responds to methods such as converEurToRon($amount), which is
     * translated to convert(1, 'EUR', 'RON');
     *
     * @param  string $method
     * @param  array  $args
     * @return float  With precision 4 (four)
     */
    public function __call($method, $args)
    {
        $currencies = $this->extractCurrenciesFrom($method);
        return $this->convert($args[0], $currencies[0], $currencies[1]);
    }

    /**
     * @param  string $method
     * @return array With two elements, for each of the two currencies
     */
    public function extractCurrenciesFrom($method) {
        $method     = str_replace('convert', '', $method);
        $currencies = explode('To', $method);
        $currencies = array_map('strtoupper', $currencies);

        return $currencies;
    }

    /**
     * @see    http://www.iso.org/iso/support/currency_codes_list-1.htm
     * @param  string $currencyCode ISO 4217 currency code
     * @return DOMElement
     */
    protected function getRateElement($currencyCode)
    {
        $this->initXPath();

        $items = $this->xpath->query("//bnr:Rate[@currency='$currencyCode']");

        if ($items->length === 0) {
            throw new RuntimeException(
                "Couldn't find a currency rate for: $currencyCode. " .
                "Make sure you have supplied a valid ISO 4217 currency code"
            );
        }

        return $items->item(0);
    }

    /**
     * @return string
     * @throws RuntimeException if one or more OrigCurrency elems are found
     */
    public function getOriginalCurrency()
    {
        if ($this->originalCurrency === null) {
            $this->initXPath();

            $originalCurrency = $this->xpath->query('//bnr:OrigCurrency');

            if ($originalCurrency->length !== 1) {
                throw new RuntimeException(
                    'None or multiple occurences of XML element OrigCurrency'
                );
            }

            $this->originalCurrency = strtoupper($originalCurrency->item(0)->textContent);
        }

        return $this->originalCurrency;
    }

    /**
     * Returns whether $code appears in the OrigCurrency element of the XML.
     *
     * @param  string $code ISO currency code
     * @return boolean
     */
    public function isOriginalCurrency($code)
    {
        return $this->getOriginalCurrency() === strtoupper($code);
    }

    /**
     * @return void
     */
    protected function initXPath()
    {
        if ($this->xpath === null) {
            $dom = new DOMDocument;
            $dom->loadXML($this->xmlSource);
            $this->xpath = new DOMXPath($dom);
            $this->xpath->registerNamespace('bnr', 'http://www.bnr.ro/xsd');
        }
    }
}

/**
 * @param  string $xmlSource
 * @return CurrencyConverter
 */
function CurrencyConverter($xmlSource)
{
    return new CurrencyConverter($xmlSource);
}
