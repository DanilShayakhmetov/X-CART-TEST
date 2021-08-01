<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Module\XC\MultiVendor\View\VendorInfo;


/**
 * Class Product
 *
 * @Decorator\Depend ("XC\MultiVendor")
 */
class Product extends \XLite\Module\XC\MultiVendor\View\VendorInfo\Product implements \XLite\Base\IDecorator
{
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'modules/XC/VendorMessages/vendor_info/style.less'
            ]
        );
    }
}
