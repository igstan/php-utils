<?php

class CurrencyRateFetcher
{
    /**
     * @var string
     */
    private $xmlPath;

    /**
     * @var object
     */
    private $cacheEngine;

    /**
     * @var string
     */
    private $fetchFunction;

    /**
     * @var integer UNIX timestamp or null
     */
    private $now;

    /**
     * @var string
     */
    private $cacheKey;

    /**
     * @param  string   $xmlPath
     * @param  object   $cacheEngine
     * @param  callable $fetchFunction Defaults to file_get_contents
     * @param  integer  $now           Timestamp to use as current time
     * @throws InvalidArgumentException on invalid XML path
     */
    public function __construct($xmlPath, $cacheEngine, $fetchFunction = 'file_get_contents', $now = null)
    {
        $this->setXmlPath($xmlPath);
        $this->setCacheEngine($cacheEngine);
        $this->setCacheKey();

        $this->fetchFunction = $fetchFunction;
        $this->now           = (int)$now;
    }

    /**
     * @return string
     */
    public function getXmlPath()
    {
        return $this->xmlPath;
    }

    /**
     * @param  string $xmlPath
     * @throws InvalidArgumentException on invalid XML path
     */
    protected function setXmlPath($xmlPath)
    {
        if (! $this->isValidPath($xmlPath)) {
            throw new InvalidArgumentException(
                "Supplied XML file path does not seem to be reachable. Path: $xmlPath"
            );
        }

        $this->xmlPath = $xmlPath;
    }

    /**
     * @param  string $xmlPath
     * @return boolean
     */
    protected function isValidPath($xmlPath)
    {
        return $this->isValidUrl($xmlPath) || is_file($xmlPath);
    }

    /**
     * @param  string $url
     * @return boolean
     */
    protected function isValidUrl($url)
    {
        $options = FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED;
        return (bool)filter_var($url, FILTER_VALIDATE_URL, $options);
    }

    /**
     * @param object $cacheEngine
     */
    protected function setCacheEngine($cacheEngine)
    {
        $this->cacheEngine = $cacheEngine;
    }

    /**
     * @return void
     */
    protected function setCacheKey()
    {
        $this->cacheKey = md5($this->xmlPath);
    }

    /**
     * @return integer
     */
    protected function getNow()
    {
        return $this->now ? $this->now : time();
    }

    /**
     * See the tests for the rules applied in this method.
     *
     * @return boolean
     */
    protected function cacheHasExpired($saveTime)
    {
        $validCache   = false;
        $expiredCache = true;
        $rightNow     = $this->getNow();
        $today13      = strtotime('today, 13:00:00', $rightNow);
        $yesterday13  = strtotime('yesterday, 13:00:00', $rightNow);

        if ($rightNow < $today13) {
            if ($yesterday13 < $saveTime) {
                return $validCache;
            } else {
                return $expiredCache;
            }
        } else {
            if ($saveTime < $today13) {
                return $expiredCache;
            } else {
                return $validCache;
            }
        }
    }

    /**
     * @return string
     */
    public function getContents()
    {
        $contents = $this->cacheEngine->load($this->cacheKey);
    
        if (!$contents || $this->cacheHasExpired($contents->saveTime)) {
            $contents = (object)array(
                'xmlSource' => call_user_func($this->fetchFunction, $this->getXmlPath()),
                'saveTime'  => $this->getNow(),
            );

            $this->cacheEngine->save($contents, $this->cacheKey);
        }

        return $contents->xmlSource;
    }

    public function __toString()
    {
        try {
            return $this->getContents();
        } catch (Exception $e) {
            return 'Exception occurred: ' . $e->getMessage();
        }
    }
}

/**
 * @param  string   $xmlPath
 * @param  string   $cache
 * @param  callable $fetchFunction Defaults to file_get_contents
 * @param  integer  $now           Timestamp to use as current time
 * @return CurrencyRateFetcher
 */
function CurrencyRateFetcher($xmlPath, $cache, $fetchFunction = 'file_get_contents', $now = null)
{
    return new CurrencyRateFetcher($xmlPath, $cache, $fetchFunction, $now);
}
