<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic;

use XLite\Core\Cache\ExecuteCached;
use XLite\Logic\WhoisServices\Whoisxmlapi;

abstract class WhoisService
{
    const CACHE_LIFETIME = 86400;

    /**
     * @var string
     */
    protected $domain;

    public static function create(string $domain, string $service = Whoisxmlapi::class): self
    {
        return new $service($domain);
    }

    /**
     * WhoisService constructor.
     *
     * @param string $domain
     */
    public function __construct(string $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    abstract public function getApiUri(): string;

    /**
     * @return array
     */
    abstract public function getStatus(): array;

    /**
     * @return array
     */
    abstract public function getNameServers(): array;

    /**
     * @return string
     */
    abstract public function getRegistrarWhoisServer(): string;

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return ExecuteCached::executeCached(function () {
            $client  = new \GuzzleHttp\Client();
            $request = $client->request('GET', $this->getApiUri());

            if ($request->getStatusCode() !== 200) {
                throw new \Exception('Whois server unavailable');
            }

            $body = $request->getBody()->getContents();

            return \GuzzleHttp\json_decode($body, true);
        }, $this->domain, static::CACHE_LIFETIME);
    }
}