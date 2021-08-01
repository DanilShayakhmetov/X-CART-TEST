<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Base;

/**
 * Video abstract repository
 */
abstract class Video extends \XLite\Model\Repo\Base\Storage
{
    /**
     * Get file system images storage root path
     *
     * @return string
     */
    public function getFileSystemRoot()
    {
        return LC_DIR_FILES . $this->getStorageName() . LC_DS;
    }

    /**
     * Get web images storage root path
     *
     * @return string
     */
    public function getWebRoot()
    {
        return LC_FILES_URL . '/' . $this->getStorageName() . '/';
    }
}

