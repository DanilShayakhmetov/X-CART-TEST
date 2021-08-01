<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\Logic\ImageResize;


/**
 * Generator
 */
class Generator extends \XLite\Logic\ImageResize\Generator implements \XLite\Base\IDecorator
{
    /**
     * Returns available image sizes
     *
     * @return array
     */
    public static function defineImageSizes()
    {
        $result = parent::defineImageSizes();
        $result[static::MODEL_PRODUCT]['RecentlyViewedThumbnail'] = [120, 120];

        return $result;
    }
}