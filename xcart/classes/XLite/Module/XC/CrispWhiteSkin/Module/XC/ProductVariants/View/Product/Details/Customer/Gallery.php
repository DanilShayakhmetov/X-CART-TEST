<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\Module\XC\ProductVariants\View\Product\Details\Customer;


/**
 * Gallery
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
class Gallery extends \XLite\View\Product\Details\Customer\Gallery implements \XLite\Base\IDecorator
{
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/XC/ProductVariants/product/cycle-gallery.js'
        ]);
    }

}