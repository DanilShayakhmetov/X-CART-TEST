<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\WhoisServices;

use XLite\Logic\WhoisService;

class Whoisxmlapi extends WhoisService
{
    /**
     * Free api key (500 requests per month)
     */
    public const API_KEY = 'at_4esD14hWIW9i76YshYzHZ640gos6O';

    /**
     * @return string
     */
    public function getApiUri(): string
    {
        $params = http_build_query([
            'apiKey'       => static::API_KEY,
            'domainName'   => $this->domain,
            'outputFormat' => 'json',
        ]);

        return "https://www.whoisxmlapi.com/whoisserver/WhoisService?$params";
    }

    /**
     * @return array
     */
    public function getStatus(): array
    {
        $info = $this->getInfo();

        if (isset($info['ErrorMessage'])) {
            return [
                'errCode' => $info['ErrorMessage']['errorCode'],
                'errMsg' => $info['ErrorMessage']['msg'],
            ];
        }

        if (isset($info['WhoisRecord']['dataError'])) {
            return $info['WhoisRecord']['dataError'] === 'MISSING_WHOIS_DATA'
                ? ['isAvailable' => true]
                : ['errCode' => $info['WhoisRecord']['dataError']];
        }

        return ['isAvailable' => false];
    }

    /**
     * @return array
     */
    public function getNameServers(): array
    {
        $info = $this->getInfo();

        return $info['WhoisRecord']['registryData']['nameServers']['hostNames'] ?? [];
    }

    /**
     * @return string
     */
    public function getRegistrarWhoisServer(): string
    {
        $info = $this->getInfo();

        return $info['WhoisRecord']['registryData']['whoisServer'] ?? '';
    }
}