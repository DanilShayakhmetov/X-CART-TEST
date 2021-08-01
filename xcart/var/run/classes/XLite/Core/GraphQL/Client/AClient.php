<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\GraphQL\Client;

use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Request;

/**
 * Abstract GraphQL client
 */
abstract class AClient
{
    /**
     * @var \XLite\Core\GraphQL\ResponseBuilder
     */
    protected $responseBuilder;

    /**
     * @var \GuzzleHttp\Client
     */
    private $httpClient;

    /**
     * AClient constructor.
     *
     * @param \GuzzleHttp\Client                  $httpClient
     * @param \XLite\Core\GraphQL\ResponseBuilder $responseBuilder
     */
    public function __construct($httpClient, $responseBuilder)
    {
        $this->httpClient      = $httpClient;
        $this->responseBuilder = $responseBuilder;
    }

    /**
     * @param string $query
     * @param array  $variables
     *
     * @return \XLite\Core\GraphQL\Response
     * @throws \XLite\Core\Exception
     * @throws \XLite\Core\GraphQL\Exception\UnexpectedValue
     */
    public function query($query, $variables = [])
    {
        return $this->performRequest($this->prepareOptions([
            'json' => [[
                'query'     => $query,
                'variables' => $variables,
            ]],
        ]));
    }

    /**
     * @param $options
     *
     * @return array
     */
    protected function prepareOptions(array $options)
    {
        return $options;
    }

    private function performRequest($requestData)
    {
        try {
            $response = $this->httpClient->post(null, $requestData);

            if ($response->getStatusCode() !== 200) {
                throw new \GuzzleHttp\Exception\BadResponseException(
                    "GraphQL authorization request failed: expected HTTP code \"200\" received \"{$response->getStatusCode()}\"",
                    new Request('POST', null),
                    $response
                );
            }
        } catch (TransferException $e) {
            throw new \XLite\Core\Exception(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $this->responseBuilder->build($response->getBody()->getContents());
    }
}
