<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Auth;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use XCart\Bus\Client\XCart;
use XCart\Bus\Exception\XC5Unavailable;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class XC5LoginService
{
    /**
     * @var XCart
     */
    protected $client;

    /**
     * @var array
     */
    protected $verifyData;

    /**
     * @param XCart $client
     */
    public function __construct(XCart $client)
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getCookieName(): string
    {
        return $this->client->getCookieName();
    }

    /**
     * @return string
     */
    public function getLoginURL(): string
    {
        return $this->client->getLoginURL();
    }

    /**
     * @param $cookie
     *
     * @return bool
     * @throws XC5Unavailable
     */
    public function checkXC5Cookie($cookie)
    {
        try {
            $decoded = $this->getVerifyData($cookie);

            $result = false;
            if ($decoded) {
                $result = isset($decoded['authorized'])
                    && $decoded['authorized'] === true;
            }

            return $result;

        } catch (ClientException $e) {
            if ($e->getResponse() !== null) {
                if ($e->getResponse()->getStatusCode() === 401) {
                    return false;
                }

                if ($e->getResponse()->getStatusCode() !== 200) {
                    throw new XC5Unavailable('', $e->getResponse()->getStatusCode(), $e);
                }
            }

            throw $e;

        } catch (BadResponseException $e) {
            throw new XC5Unavailable('', 0, $e);
        }
    }

    /**
     * @param $cookie
     *
     * @return bool
     * @throws XC5Unavailable
     */
    public function getVerifyData($cookie)
    {
        if ($this->verifyData === null) {
            $this->verifyData = $this->getVerifyDataReal($cookie);
        }

        return $this->verifyData;
    }

    /**
     * @param $cookie
     *
     * @return bool
     * @throws XC5Unavailable
     */
    public function getVerifyDataReal($cookie)
    {
        try {
            $this->client->setAuthCookie($cookie);
            $response = $this->client->getVerifyCookie();

            $result = null;

            if ($response
                && $response->getStatusCode() === 200
            ) {
                $result = json_decode($response->getBody(), true);
            }

            return $result;

        } catch (ClientException $e) {
            if ($e->getResponse() !== null
                && $e->getResponse()->getStatusCode() === 401
            ) {
                return false;
            }

            throw $e;

        } catch (BadResponseException $e) {
            throw new XC5Unavailable("", 0, $e);
        }
    }
}
