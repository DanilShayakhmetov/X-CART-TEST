<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Helper;

use Silex\Application;
use XCart\SilexAnnotations\Annotations\Service;
use const PHP_URL_HOST;

/**
 * @Service\Service()
 */
class UrlBuilder
{
    /**
     * @var string
     */
    private $endpoint = 'service.php';

    /**
     * @var string
     */
    private $adminScript;

    /**
     * @var Application
     */
    private $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app         = $app;
        $this->adminScript = $app['config']['admin_script'];
    }

    /**
     * @return string
     */
    public function buildAdminUrl(): string
    {
        return $this->buildMainUrl() . '/' . $this->adminScript;
    }

    /**
     * @param $target
     *
     * @return string
     */
    public function buildServiceUrl($target = ''): string
    {
        $url = $this->buildMainUrl() . '/' . $this->endpoint . '#/';

        if ($target) {
            $url = $url . '' . $target;
        }

        return $url;
    }

    /**
     * @param $target
     *
     * @return string
     */
    public function buildServiceMainUrl($target = ''): string
    {
        $url = $this->buildMainUrl() . '/' . $this->endpoint . '?/';

        if ($target) {
            $url = $url . '' . $target;
        }

        return $url;
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public function isSelfURL($url): bool
    {
        $host   = parse_url($url, PHP_URL_HOST);
        $domain = parse_url($this->app['config']['domain'], PHP_URL_HOST);

        return !$host || $host === $domain;
    }

    /**
     * @return string
     */
    protected function buildMainUrl(): string
    {
        $url = trim($this->app['config']['domain'], '/');
        $webDir = trim($this->app['config']['webdir'], '/');

        if ($webDir) {
            $url .= '/' . $webDir;
        }

        return $url;
    }
}
