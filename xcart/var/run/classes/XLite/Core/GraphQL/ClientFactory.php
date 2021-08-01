<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\GraphQL;


/**
 * ClientFactory
 */
class ClientFactory
{
    /**
     * @param string $endpoint
     *
     * @return \XLite\Core\GraphQL\Client\Simple
     */
    public static function create($endpoint)
    {
        return new Client\Simple(
            new \GuzzleHttp\Client(['base_uri' => $endpoint] + static::getGuzzleClientDefaults()),
            new ResponseBuilder()
        );
    }

    /**
     * @param string $endpoint
     * @param string $authEndpoint
     * @param string $authCode
     *
     * @return \XLite\Core\GraphQL\Client\WithBusAuth
     */
    public static function createWithBusAuth($endpoint, $authEndpoint, $authCode)
    {
        return new Client\WithBusAuth(
            new \GuzzleHttp\Client(
                [
                    'base_uri'        => $endpoint,
                    'timeout'         => 45,
                ] + static::getGuzzleClientDefaults()),
            new ResponseBuilder(),
            new \GuzzleHttp\Client(
                [
                    'base_uri'        => $authEndpoint,
                    'timeout'         => 45,
                ] + static::getGuzzleClientDefaults()),
            $authCode
        );
    }

    protected static function getGuzzleClientDefaults()
    {
        $defaults = [
            'verify'  => (bool) \Includes\Utils\ConfigParser::getOptions(['service', 'verify_certificate']),
            'handler' => \GuzzleHttp\HandlerStack::create(
                new \GuzzleHttp\Handler\CurlHandler([
                    'handle_factory' => new \GuzzleHttp\Handler\CurlFactory(0),
                ])
            ),
        ];

        if (
            ($authUser = \Includes\Utils\ConfigParser::getOptions(['service', 'basic_auth_user']))
            && ($authPass = \Includes\Utils\ConfigParser::getOptions(['service', 'basic_auth_pass']))
        ) {
            $defaults['auth'] = [
                $authUser,
                $authPass,
            ];
        }

        return $defaults;
    }
}