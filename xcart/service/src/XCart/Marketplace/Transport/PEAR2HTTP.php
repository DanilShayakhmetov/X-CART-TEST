<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Transport;

class PEAR2HTTP extends AHTTP
{
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
        $request = new \PEAR2\HTTP\Request($url);

        $request->verb = $verb;

        if ($data && $verb !== 'GET') {
            $request->body = $data;
        }

        if ($ttl) {
            $request->requestTimeout = $ttl;
        }

        foreach ($headers as $header => $value) {
            $request->setHeader($header, $value);
        }

        try {
            /** @var \PEAR2\HTTP\Request\Response $response */
            $response = $request->sendRequest();

            if ($response && (int) $response->code === 200) {

                $headers = [];

                foreach ($response->headers as $header => $value) {
                    $headers[$header][] = $value;
                }

                return [
                    'headers' => $headers,
                    'body' => $response->body
                ];
            }

            if ($response && $response->code) {

                throw new TransportException(
                    sprintf('Unsuccess response code returned (%s)', $response->code),
                    $response->code
                );
            }

            throw new TransportException('Some error occurred');

        } catch (\PEAR2\HTTP\Request\Exception $e) {

            throw new TransportException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
