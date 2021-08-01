<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Check if product price in list should be displayed as range
     *
     * @return bool
     */
    public static function isDisplayPriceAsRange()
    {
        return \XLite\Core\Config::getInstance()->XC->ProductVariants->price_in_list
            === \XLite\Module\XC\ProductVariants\View\FormField\Select\PriceInList::DISPLAY_RANGE;
    }
}
