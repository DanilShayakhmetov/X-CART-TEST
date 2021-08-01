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
 * Class GoogleDrive
 * https://drive.google.com/open?id=1wKNYkMyCxWmx9m07cPEGL-ut09v1UkPzhg
 */
class GoogleDrive extends AURL
{
    /**
     * @param string $url
     *
     * @return boolean
     */
    public static function isMatch($url)
    {
        return static::isURL($url) && preg_match('/^https?:\/\/drive.google.com\//', $url);
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function convertURL($url)
    {
        $urlParts = parse_url($url);

        if ($urlParts['path'] === '/open') {
            return $urlParts['scheme'] . '://' . $urlParts['host'] . '/uc?export=download&' . $urlParts['query'];
        }

        if ($urlParts['path'] === '/uc') {
            return $urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path'] . '?' . $urlParts['query']; //same as $url but parsed
        }

        if (preg_match('/file\/d\/([a-zA-Z0-9_-]+)\//', $urlParts['path'], $matches)) {
            return $urlParts['scheme'] . '://' . $urlParts['host'] . '/uc?export=download&id=' . $matches[1];
        }

        return '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        try {
            $headers            = $this->getHeaders();
            $contentDisposition = $headers->ContentDisposition;

            if (preg_match('/filename\*=(?:[^\']+)\'\'(\S+)/', $contentDisposition, $matches)) {

                return urldecode($matches[1]);
            }

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
