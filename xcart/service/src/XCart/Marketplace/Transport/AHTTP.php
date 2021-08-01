<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Transport;

use XCart\Marketplace\ITransport;

abstract class AHTTP implements ITransport
{
    const ENDPOINT = 'endpoint';

    /**
     * @var array
     */
    protected $config;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge(
            [
                static::ENDPOINT => '',
            ],
            $config
        );
    }

    /**
     * @param string $path
     *
     * @return array
     * @throws TransportException
     */
    public function getFileContent($path)
    {
        return $this->doRequest('GET', $this->createFileRequestURL($path));
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param string $action
     * @param array  $data
     * @param array  $headers
     * @param int    $ttl
     *
     * @return array
     * @throws TransportException
     */
    public function doAPIRequest($action, array $data = [], array $headers = [], $ttl = self::TTL_DEFAULT)
    {
        return $this->doRequest('POST', $this->createAPIRequestURL($action), $data, $headers, $ttl);
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param string     $verb
     * @param string     $url
     * @param array|null $data
     * @param array      $headers
     * @param int        $ttl
     *
     * @return array
     * @throws TransportException
     */
    abstract protected function doRequest($verb, $url, $data = null, array $headers = [], $ttl = self::TTL_DEFAULT);

    /**
     * @param string $action
     *
     * @return string
     * @throws TransportException
     */
    protected function createAPIRequestURL($action)
    {
        $endPoint = $this->config[self::ENDPOINT];

        if (!$this->isValidURL($endPoint)) {

            throw new TransportException('Endpoint URL is not valid');
        }

        return rtrim($endPoint, '/') . '/' . $action;
    }

    /**
     * @param string $path
     *
     * @return string
     * @throws TransportException
     */
    protected function createFileRequestURL($path)
    {
        $endPoint = $this->config[self::ENDPOINT];

        if (!$this->isValidURL($endPoint)) {

            throw new TransportException('Endpoint URL is not valid');
        }

        return preg_replace('/\/[^\/]+$/US', $path, str_replace('https://', 'http://', $endPoint));
    }

    /**
     * @param $url
     *
     * @return bool
     */
    protected function isValidURL($url)
    {
        return (bool) filter_var($url, \FILTER_VALIDATE_URL);
    }
}
