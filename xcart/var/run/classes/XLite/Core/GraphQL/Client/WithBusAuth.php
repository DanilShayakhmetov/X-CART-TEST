<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\GraphQL\Client;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\TransferStats;
use XLite\Core\GraphQL\Exception\UnableToAuthorize;

/**
 * GraphQL client with BUS - like authentication
 */
class WithBusAuth extends Simple
{
    const BUS_TOKEN = 'bus_token';

    /**
     * @var string
     */
    private $token;

    /**
     * @var \GuzzleHttp\Client
     */
    private $authClient;

    /**
     * @var string
     */
    private $authCode;

    /**
     * WithBusAuth constructor.
     *
     * @param \GuzzleHttp\Client                  $httpClient
     * @param \XLite\Core\GraphQL\ResponseBuilder $responseBuilder
     * @param \GuzzleHttp\Client                  $authClient
     * @param string                              $authCode
     */
    public function __construct($httpClient, $responseBuilder, $authClient, $authCode)
    {
        parent::__construct($httpClient, $responseBuilder);

        $this->authClient = $authClient;
        $this->authCode = $authCode;
    }

    protected function prepareOptions(array $options)
    {
        $options['cookies'] = \GuzzleHttp\Cookie\CookieJar::fromArray(
            [
                static::BUS_TOKEN => $this->getToken(),
            ],
            parse_url($this->authClient->getConfig('base_uri'), PHP_URL_HOST)
        );

        return parent::prepareOptions($options);
    }


    /**
     * @return string
     */
    protected function getToken()
    {
        if (is_null($this->token)) {
            $this->token = $this->retrieveToken();
        }

        return $this->token;
    }

    /**
     * Auth request
     * @return string
     * @throws \XLite\Core\Exception
     */
    private function retrieveToken()
    {
        try {
            $jar = new \GuzzleHttp\Cookie\CookieJar();

            $request = null;
            $response = $this->authClient->post('', [
                'form_params' => [
                    'auth_code' => $this->authCode,
                ],
                'cookies' => $jar,
                'on_stats' => static function (TransferStats $stats) use (&$request) {
                    $request = $stats->getRequest();
                }
            ]);

            if ($request && $response && $response->getStatusCode() !== 200) {
                throw new \GuzzleHttp\Exception\BadResponseException(
                    "GraphQL authorization request failed: expected HTTP code \"200\" received \"{$response->getStatusCode()}\"",
                    $request,
                    $response
                );
            }

            return $this->retrieveBusTokenFromCookie($jar);
        } catch (TransferException $e) {
            throw new \XLite\Core\Exception(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param \GuzzleHttp\Cookie\CookieJar $jar
     *
     * @return mixed
     * @throws \XLite\Core\GraphQL\Exception\UnableToAuthorize
     */
    private function retrieveBusTokenFromCookie($jar)
    {
        /* @var \GuzzleHttp\Cookie\SetCookie $item */
        foreach ($jar->getIterator() as $item) {
            if ($item->getName() === static::BUS_TOKEN) {
                return $item->getValue();
            }
        }

        throw new UnableToAuthorize('GraphQL authorization request failed: BUS token cookie not found');
    }
}