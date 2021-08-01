<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Module\XC\ThemeTweaker\Core\Notifications;

use XLite\Module\XC\ProductVariants\Core\Notifications\Data\ProductVariant;


/**
 * Data
 *
 * @Decorator\Depend("XC\ThemeTweaker")
 */
class Data extends \XLite\Module\XC\ThemeTweaker\Core\Notifications\Data implements \XLite\Base\IDecorator
{
    protected function defineProviders()
    {
        return array_merge(parent::defineProviders(), [
            new ProductVariant()
        ]);
    }
}