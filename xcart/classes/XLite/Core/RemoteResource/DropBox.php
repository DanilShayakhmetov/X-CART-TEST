<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\RemoteResource;

use PEAR2\HTTP\Request\Exception;

/**
 * Class DropBox
 * https://www.dropbox.com/s/[resource_id]/[file_name]?dl=0
 */
class DropBox extends AURL
{
    /**
     * @param string $url
     *
     * @return boolean
     */
    public static function isMatch($url)
    {
        return static::isURL($url) && preg_match('/^https?:\/\/(www\.)?dropbox.com\//', $url);
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function convertURL($url)
    {
        $urlParts = parse_url($url);

        return $urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path'] . '?raw=1';
    }

    /**
     * @return string
     */
    public function getName()
    {
        try {
            $headers            = $this->getHeaders();
            $contentDisposition = $headers->ContentDisposition;

            if (preg_match('/filename\*=(?:[^\']+)\'\'(\S+)/', $contentDisposition, $matches)
                || preg_match('/filename="(.*)(?(?=\\\\)[^\"]|\")/', $contentDisposition, $matches)
            ) {

                return $matches[1];
            }
        } catch (Exception $e) {
        }

        return parent::getName();
    }
}
