<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\RemoteResource;

use PEAR2\HTTP\Request\Headers;

interface IURL
{
    /**
     * @param string $url
     *
     * @return boolean
     */
    public static function isMatch($url);

    /**
     * @param string $url
     */
    public function __construct($url);

    /**
     * @return boolean
     */
    public function isAvailable();

    /**
     * @return string
     */
    public function getURL();

    /**
     * @return Headers
     */
    public function getHeaders();

    /**
     * @return string
     */
    public function getName();
}
