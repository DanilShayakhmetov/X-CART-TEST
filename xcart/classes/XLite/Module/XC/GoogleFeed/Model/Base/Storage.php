<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\Model\Base;

use XLite\Core\RemoteResource\IURL;
use XLite\Core\RemoteResource\RemoteResourceException;
use XLite\Core\RemoteResource\RemoteResourceFactory;
use XLite\Module\XC\GoogleFeed\Main;

/**
 * Storage abstract store
 */
abstract class Storage extends \XLite\Model\Base\Storage implements \XLite\Base\IDecorator
{
    /**
     * Get URL
     *
     * @return string
     */
    public function getGoogleFeedURL()
    {
        $url = null;

        if ($this->isURL()) {
            $url = $this->getPath();
        } elseif (static::STORAGE_RELATIVE == $this->getStorageType()) {
            $url = Main::getShopURL(
                $this->getWebRoot() . $this->convertPathToURL($this->getPath())
            );
        } else {
            $root = $this->getFileSystemRoot();
            if (0 === strncmp($root, $this->getPath(), strlen($root))) {
                $path = substr($this->getPath(), strlen($root));
                $url = Main::getShopURL(
                    $this->getWebRoot() . $this->convertPathToURL($path)
                );
            } else {
                $url = $this->getGetterURL();
            }
        }

        return \XLite\Core\Converter::makeURLValid($url);
    }
}
