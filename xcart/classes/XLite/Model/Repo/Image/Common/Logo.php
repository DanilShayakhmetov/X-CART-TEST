<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Image\Common;


class Logo extends \XLite\Model\Repo\Base\Image
{
    /**
     * Get storage name
     *
     * @return string
     */
    public function getStorageName()
    {
        return 'logo';
    }

    /**
     * Get file system images storage root path
     *
     * @return string
     */
    public function getFileSystemRoot()
    {
        return LC_DIR_ROOT;
    }

    /**
     * Get web images storage root path
     *
     * @return string
     */
    public function getWebRoot()
    {
        return '';
    }

    /**
     * @return \XLite\Model\Image\Common\Logo
     */
    public function getLogo()
    {
        $path = \XLite\Core\Layout::getInstance()->getLogo();

        return $path
            ? self::getFakeImageObject($path)
            : null;
    }
    
    /**
     * @return \XLite\Model\Image\Common\Logo
     */
    public function getFavicon()
    {
        $path = \XLite\Core\Layout::getInstance()->getFavicon();

        return $path
            ? self::getFakeImageObject($path)
            : null;
    }

    /**
     * @return \XLite\Model\Image\Common\Logo
     */
    public function getAppleIcon()
    {
        $path = \XLite\Core\Layout::getInstance()->getAppleIcon();

        return $path
            ? self::getFakeImageObject($path)
            : null;
    }

    /**
     * @param $path
     *
     * @return \XLite\Model\Image\Common\Logo
     */
    public static function getFakeImageObject($path)
    {
        $obj = new \XLite\Model\Image\Common\Logo([
            'id'          => 1,
            'path'        => $path,
            'storageType' => 'r',
            'date'        =>  \XLite::getLastRebuildTimestamp(),
        ]);

        $obj->updateDimensionsSizes();
        $obj->updateMimeType();

        return $obj;
    }
}
