<?php

require_once 'CurrencyRateFetcher.php';

class CurrencyRateFetcherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testSupplyingABadFormedUrlThrowsException()
    {
        CurrencyRateFetcher('bad-formed-url', new StdClass);
    }

    public function testValidUrlGetsAssignedInternally()
    {
        $xmlPath = 'http://example.com/';
        $fetcher = CurrencyRateFetcher($xmlPath, new StdClass);

        $this->assertEquals($xmlPath, $fetcher->getXmlPath());
    }

    public function testReturnsCorrectContents()
    {
        $cache = $this->makeMockCacheEngine(array(
            'saveTime'  => 'July 20, 2009 11:00:00',
            'xmlSource' => 'Does not matter',
        ));
        $fetcher = CurrencyRateFetcher('http://example.com/', $cache, array($this, 'fetchFunction'));

        $this->assertEquals('Hello World', $fetcher->getContents());
    }

    public function testToStringMethod()
    {
        $cache = $this->makeMockCacheEngine(array(
            'saveTime'  => 'July 20, 2009 11:00:00',
            'xmlSource' => 'Does not matter',
        ));
        $fetcher = CurrencyRateFetcher('http://example.com/', $cache, array($this, 'fetchFunction'));

        $this->assertEquals('Hello World', (string)$fetcher);
    }

    /**
     * Tests the following cache rule:
     *
     * Current hour is less than 13 and last cache save was after the previous
     * 13-hour mark. That means, it should fetch from cache.
     */
    public function testCacheWhenTimeBefore13AndLastCacheWasAfterLast13()
    {
        $cache = $this->makeMockCacheEngine(array(
            'saveTime'  => 'July 19, 2009 21:00:00',
            'xmlSource' => 'This is cached content',
        ));

        $fetcher = CurrencyRateFetcher(
            'http://example.com/',
            $cache,
            array($this, 'freshContent'),
            $now = strtotime('July 20, 2009 11:00:00')
        );

        $this->assertEquals('This is cached content', $fetcher->getContents());
    }

    /**
     * Tests the following cache rule:
     *
     * Current hour is less than 13 and last cache save was before the previous
     * 13-hour mark. That means it should refresh the cache.
     */
    public function testCacheWhenTimeBefore13AndLastCacheWasBeforeLast13()
    {
        $cache = $this->makeMockCacheEngine(array(
            'saveTime'  => 'July 19, 2009 11:00:00',
            'xmlSource' => 'This is cached content',
        ));

        $fetcher = CurrencyRateFetcher(
            'http://example.com/',
            $cache,
            array($this, 'freshContent'),
            $now = strtotime('July 20, 2009 11:00:00')
        );

        $this->assertEquals('This is fresh content', $fetcher->getContents());
    }

    /**
     * Tests the following cache rule:
     *
     * Current hour is greater than 13 and last cache save was after the
     * last 13-hour mark. That means, it should fetch the cache.
     */
    public function testCacheWhenTimeAfter13AndLastCacheWasAfterLast13()
    {
        $cache = $this->makeMockCacheEngine(array(
            'saveTime'  => 'July 20, 2009 14:00:00',
            'xmlSource' => 'This is cached content',
        ));

        $fetcher = CurrencyRateFetcher(
            'http://example.com/',
            $cache,
            array($this, 'freshContent'),
            $now = strtotime('July 20, 2009 15:00:00')
        );

        $this->assertEquals('This is cached content', $fetcher->getContents());
    }

    /**
     * Tests the following cache rule:
     *
     * Current hour is greater than 13 and last cache save was before the
     * last 13-hour mark. That means, it should refresh the cache.
     */
    public function testCacheWhenTimeAfter13AndLastCacheWasBeforeLast13()
    {
        $cache = $this->makeMockCacheEngine(array(
            'saveTime'  => 'July 20, 2009 11:00:00',
            'xmlSource' => 'This is cached content',
        ));

        $fetcher = CurrencyRateFetcher(
            'http://example.com/',
            $cache,
            array($this, 'freshContent'),
            $now = strtotime('July 20, 2009 15:00:00')
        );

        $this->assertEquals('This is fresh content', $fetcher->getContents());
    }

    public function fetchFunction($returnValue)
    {
        return 'Hello World';
    }

    public function freshContent()
    {
        return 'This is fresh content';
    }

    /**
     * The $specs param allows customization of the returned mock by specifying
     * the returned object.
     *
     * @param  array $specs
     * @return object Cache engine mock
     */
    protected function makeMockCacheEngine($specs)
    {
        $mock = $this->getMock('StdClass',
                                $mockMethods = array('save', 'load'),
                                $arguments = array(),
                                $mockClassName = '',
                                $callOriginalConstructor = false);

        $mock->expects($this->any())
             ->method('load')
             ->will($this->returnValue((object)array(
                    'saveTime'  => strtotime($specs['saveTime']),
                    'xmlSource' => $specs['xmlSource'],
               )));

        return $mock;
    }
}
