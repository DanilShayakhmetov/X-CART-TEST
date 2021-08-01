<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Video;

class Temporary extends \XLite\Model\Repo\Base\Video
{
    /**
     * Get storage name
     *
     * @return string
     */
    public function getStorageName()
    {
        return 'temporary_video';
    }

    /**
     * Get file system images storage root path
     *
     * @return string
     */
    public function getFileSystemRoot()
    {
        return $this->getCachePath() . $this->getStorageName() . LC_DS;
    }

    /**
     * Get web images storage root path
     *
     * @return string
     */
    public function getWebRoot()
    {
        return LC_VAR_URL . '/video/' . $this->getStorageName() . '/';
    }

    protected function getCachePath()
    {
        return LC_DIR_VAR . LC_DS .'video' . LC_DS;
    }
}
