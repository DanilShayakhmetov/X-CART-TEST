<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Module\XC\ThemeTweaker\View\NotificationEditor\Sidebar;


/**
 * DataSources
 *
 * @Decorator\Depend("XC\ThemeTweaker")
 */
class DataSources extends \XLite\Module\XC\ThemeTweaker\View\NotificationEditor\Sidebar\DataSources implements \XLite\Base\IDecorator
{
    protected function defineDataWidgets()
    {
        return array_merge(parent::defineDataWidgets(), [
            '\XLite\Module\XC\ProductVariants\View\NotificationEditor\Sidebar\DataSource\ProductVariant',
        ]);
    }
}