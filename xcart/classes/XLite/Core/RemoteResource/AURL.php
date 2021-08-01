<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\RemoteResource;

use PEAR2\HTTP\Request\Exception;
use PEAR2\HTTP\Request\Headers;
use XLite\Core\HTTP\Request;

abstract class AURL implements IURL
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var Headers
     */
    protected $headers;

    /**
     * @param string $url
     *
     * @return boolean
     */
    protected static function isURL($url)
    {
        return (bool) filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * @param string $url
     *
     * @return string
     */
    abstract public function convertURL($url);

    public function __construct($url)
    {
        $this->url = $this->convertURL($url);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return basename(parse_url($this->getURL(), \PHP_URL_PATH));
    }

    /**
     * @return boolean
     */
    public function isAvailable()
    {
        try {
            $headers       = $this->getHeaders();
            $contentLength = $headers->ContentLength;
        } catch (\Exception $e) {
            return false;
        }

        if (null === $contentLength) {

            return false;
        }

        if (strpos($contentLength, ',') !== false) {
            $contentArray = explode(',', $contentLength);
            return (int) end($contentArray) > 0;
        } else {
            return (int) $contentLength > 0;
        }
    }

    /**
     * @return string
     */
    public function getURL()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setURL($url)
    {
        $this->url = $url;
    }

    /**
     * @return Headers
     * @throws Exception
     */
    public function getHeaders()
    {
        if (null === $this->headers) {
            try {
                $this->headers = $this->getHeadHeaders();

            } catch (Exception $e) {
            }

            try {
                $this->headers = $this->getGetHeaders();

            } catch (Exception $e) {
            }
        }

        if ($this->headers) {

            return $this->headers;
        }

        throw new Exception();
    }

    /**
     * @return Headers
     * @throws Exception
     */
    protected function getHeadHeaders()
    {
        $bouncer       = new Request($this->getURL());
        $bouncer->verb = 'HEAD';
        $bouncer->setAdditionalOption(CURLOPT_FOLLOWLOCATION, true);

        $response = $bouncer->sendRequest();
        if ($response && $response->code === 200) {

            return $response->headers;
        }

        throw new Exception();
    }

    /**
     * @return Headers
     * @throws Exception
     */
    protected function getGetHeaders()
    {
        $bouncer       = new Request($this->getURL());
        $bouncer->verb = 'GET';
        $bouncer->setAdditionalOption(CURLOPT_FOLLOWLOCATION, true);
        $bouncer->setHeader('Range', 'bytes=0-0');

        $response = $bouncer->sendRequest();
        if ($response && in_array($response->code, [200, 206], true)) {

            return $response->headers;
        }

        throw new Exception();
    }
}
