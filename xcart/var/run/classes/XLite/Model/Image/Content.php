<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Image;

/**
 * Content images file storage
 *
 * @Entity
 * @Table  (name="content_images")
 */
class Content extends \XLite\Model\Base\Image
{
    /**
     * Check - file is image or not
     *
     * @return boolean
     */
    public function isImage()
    {
        return 0 < $this->getWidth();
    }
}
