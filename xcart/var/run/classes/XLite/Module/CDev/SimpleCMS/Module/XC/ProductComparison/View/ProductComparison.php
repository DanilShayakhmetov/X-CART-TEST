<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\Module\XC\ProductComparison\View;

/**
 * Offers import
 *
 * @Decorator\Depend ("XC\ProductComparison")
 */
 class ProductComparison extends \XLite\Module\XC\ProductComparison\View\ProductComparisonAbstract implements \XLite\Base\IDecorator
{
    public static function getDisallowedTargets()
    {
        return array_merge(
            parent::getDisallowedTargets(),
            [
                'page'
            ]
        );
    }
}
