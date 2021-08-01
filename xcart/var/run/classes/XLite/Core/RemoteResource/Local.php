<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\RemoteResource;

use PEAR2\HTTP\Request\Exception;
use PEAR2\HTTP\Request\Headers;

/**
 * Class Local
 */
class Local extends AURL
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @param string $url
     *
     * @return boolean
     */
    public static function isMatch($url)
    {
        return static::isURL($url)
            && static::isLocalDomain($url)
            && \Includes\Utils\FileManager::isFileReadable(static::getLocalPath($url));
    }

    /**
     * @return array
     */
    protected static function getLocalDomains()
    {
        return \XLite\Core\URLManager::getShopDomains();
    }

    /**
     * @param string $url
     *
     * @return boolean
     */
    protected static function isLocalDomain($url)
    {
        return in_array(parse_url($url, PHP_URL_HOST), static::getLocalDomains(), true);
    }

    /**
     * @param string $url
     *
     * @return string
     */
    protected static function getLocalPath($url)
    {
        $webDir = ltrim(\XLite::getInstance()->getOptions(['host_details', 'web_dir']) . '/', '/');

        return LC_DIR_ROOT . preg_replace('#^' . preg_quote($webDir) . '#', '', parse_url($url, PHP_URL_PATH));
    }

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        parent::__construct($url);

        $this->setPath(static::getLocalPath($this->getURL()));
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function convertURL($url)
    {
        return $url;
    }

    /**
     * @return boolean
     */
    public function isAvailable()
    {
        return \Includes\Utils\FileManager::isFileReadable($this->getPath());
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }
}
