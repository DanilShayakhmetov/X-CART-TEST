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
 * Class OneDrive
 */
class OneDrive extends AURL
{
    /**
     * @param string $url
     *
     * @return boolean
     */
    public static function isMatch($url)
    {
        return static::isURL($url)
            && (static::isOneDriveEmbed($url) || static::isOneDriveImage($url));
    }

    /**
     * @param string $url
     *
     * @return boolean
     */
    protected static function isOneDriveEmbed($url)
    {
        return preg_match('/^https?:\/\/onedrive\.live\.com\/(embed|download)\?/', $url);
    }

    /**
     * @param string $url
     *
     * @return boolean
     */
    protected static function isOneDriveImage($url)
    {
        return preg_match('/^https?:\/\/[\w\.]*livefilestore\.com\//', $url);
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function convertURL($url)
    {
        if (static::isOneDriveImage($url)) {

            return $url;
        }

        $urlParts = parse_url($url);

        return $urlParts['scheme'] . '://' . $urlParts['host'] . '/download' . '?' . $urlParts['query'];
    }

    public function getName()
    {
        try {
            $headers            = $this->getHeaders();
            $contentDisposition = $headers->ContentDisposition;

            if (preg_match('/filename="(.*)(?(?=\\\\)[^\"]|\")/', $contentDisposition, $matches)) {

                return $matches[1];
            }

        } catch (Exception $e) {
        }

        return parent::getName();
    }

    /**
     * @return Headers
     * @throws Exception
     */
    protected function getHeadHeaders()
    {
        throw new Exception();
    }
}
