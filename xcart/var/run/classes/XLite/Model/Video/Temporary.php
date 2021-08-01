<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Video;

use XLite\Core\URLManager;

/**
 * Content images file storage
 *
 * @Entity
 * @Table  (name="temporary_videos")
 */
class Temporary extends \XLite\Model\Base\Video
{
    /**
     * Check - file is image or not
     *
     * @return boolean
     */
    public function isImage()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getURL()
    {
        return is_string(parent::getURL())
            ? URLManager::addTimestampToUrl(parent::getURL())
            : null;
    }

    /**
     * Renew properties by path
     *
     * @param string $path Path
     *
     * @return void
     */
    protected function renewByPath($path)
    {
        $result = parent::renewByPath($path);

        return true;
    }
}
