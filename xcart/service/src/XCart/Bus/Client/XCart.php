<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Psr\Http\Message\ResponseInterface;
use Silex\Application;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class XCart
{
    /**
     * @var Application
     */
    private $app;

    private $clientFactory;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $authCookie;

    /**
     * @param Application $app
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(Application $app)
    {
        return new self(
            $app,
            $app['xCartClient.client-factory']
        );
    }

    /**
     * @param Application $app
     * @param             $clientFactory
     */
    public function __construct(
        Application $app,
        $clientFactory
    ) {
        $this->app           = $app;
        $this->clientFactory = $clientFactory;
    }

    /**
     * @return string
     */
    public function getCookieName(): string
    {
        return $this->app['config']['xc_cookie_name'];
    }

    /**
     * @return string
     */
    public function getLoginURL(): string
    {
        return $this->getClient()->getConfig('base_uri') . $this->app['config']['admin_script'] . '?target=login&returnToSpa=1';
    }

    /**
     * @return string
     */
    public function getUpgradeFrontendURL(): string
    {
        return $this->getClient()->getConfig('base_uri') . 'service.php?#/';
    }

    /**
     * @param string $value
     */
    public function setAuthCookie($value): void
    {
        $this->authCookie = $value;
    }

    /**
     * @return ResponseInterface
     */
    public function getVerifyCookie(): ResponseInterface
    {
        $jar = new CookieJar();
        $jar->setCookie(
            $this->buildCookie($this->authCookie)
        );

        return $this->getClient()->get($this->app['config']['admin_script'] . '?target=login&action=verify', [
            'cookies' => $jar,
        ]);
    }

    /**
     * @param string $name
     * @param string $rebuildId
     * @param string $cacheId
     *
     * @return mixed|null
     */
    public function executeRebuildStep($name, $rebuildId, $cacheId)
    {
        return $this->executeRebuildRequest(
            ['rebuildId' => $rebuildId, 'cacheId' => $cacheId],
            ['step_name' => $name]
        );
    }

    /**
     * @param string $file
     * @param array  $state
     * @param string $rebuildId
     * @param string $cacheId
     *
     * @return mixed|null
     */
    public function executeHook($file, $state, $rebuildId, $cacheId)
    {
        return $this->executeRebuildRequest(
            ['rebuildId' => $rebuildId, 'cacheId' => $cacheId],
            $state + ['file' => $file]
        );
    }

    /**
     * @param string $action
     * @param array  $params
     * @param array  $state
     * @param string $rebuildId
     * @param string $cacheId
     *
     * @return mixed|null
     */
    public function executeAction($action, $params, $rebuildId, $cacheId)
    {
        return $this->executeRebuildRequest(
            ['rebuildId' => $rebuildId, 'cacheId' => $cacheId],
            ['action' => $action, 'arg' => $params]
        );
    }

    /**
     * @param array $params
     * @param array $requestData
     *
     * @return mixed|null
     */
    public function executeRebuildRequest($params, array $requestData)
    {
        $jar = new CookieJar();
        $jar->setCookie(
            $this->buildCookie($this->authCookie)
        );

        $response = $this->getClient()->post('rebuild.php?' . http_build_query(array_filter($params)), [
            'cookies' => $jar,
            'json'    => $requestData,
        ]);

        return $response
            ? json_decode((string) $response->getBody(), true)
            : null;
    }

    /**
     * @return string
     */
    protected function getCookieDomain($domain)
    {
        $regex   = '/^(?:http:|https:)?\/\/(.*?)(:|\/|$)/i';
        $matches = [];

        if (preg_match($regex, $domain, $matches)) {
            return $matches[1] ?? $domain;
        }

        return $domain;
    }

    /**
     * @param $cookieValue
     *
     * @return SetCookie
     */
    protected function buildCookie($cookieValue)
    {
        return new SetCookie([
            'Name'   => $this->app['config']['xc_cookie_name'],
            'Value'  => $cookieValue,
            'Domain' => $this->getCookieDomain($this->app['config']['domain']),
            'Path'   => $this->app['config']['webdir'] ?: '/',
        ]);
    }

    private function getClient()
    {
        if ($this->client === null) {
            $factory      = $this->clientFactory;
            $this->client = $factory();
        }

        return $this->client;
    }
}
