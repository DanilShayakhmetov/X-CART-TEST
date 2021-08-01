<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlFactory;
use GuzzleHttp\Handler\CurlHandler;
use Psr\Http\Message\ResponseInterface;

class Guzzle extends AHTTP
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $endPoint     = $this->config[self::ENDPOINT];
        $this->client = new Client([
            'base_uri' => $endPoint,
            'handler'  => new CurlHandler([
                'handle_factory' => new CurlFactory(0),
            ]),
        ]);
    }

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
    protected function doRequest($verb, $url, $data = null, array $headers = [], $ttl = self::TTL_DEFAULT)
    {
        $params = [
            'allow_redirects' => false,
            'headers'         => $headers,
        ];

        if ($data && $verb !== 'GET') {
            $params['form_params'] = $data;
        }

        if ($ttl) {
            $params['connect_timeout'] = $ttl;
        }

        $method = strtolower($verb);

        try {
            /** @var ResponseInterface $response */
            $response = $this->client->{$method}($url, $params);

            if ($response
                && (int)$response->getStatusCode() >= 200
                && (int)$response->getStatusCode() < 300
            ) {
                return $this->processResponse($response);
            }

            if ($response && $response->getStatusCode()) {

                throw new TransportException(
                    sprintf('Unsuccess response code returned (%s)', $response->getStatusCode()),
                    $response->getStatusCode()
                );
            }

            throw new TransportException('Some error occurred');

        } catch (RequestException $e) {

            throw new TransportException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    protected function processResponse(ResponseInterface $response)
    {
        $headers = [];

        foreach ($response->getHeaders() as $header => $value) {
            $headers[strtolower($header)] = $value;
        }

        return [
            'headers' => $headers,
            'body'    => (string) $response->getBody(),
        ];
    }
}
