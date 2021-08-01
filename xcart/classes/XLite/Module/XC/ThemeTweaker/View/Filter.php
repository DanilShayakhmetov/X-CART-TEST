<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;

use XLite\Module\XC\ThemeTweaker;

/**
 * @Decorator\Depend ("XC\ProductFilter")
 */
abstract class Filter extends \XLite\Module\XC\ProductFilter\View\Filter implements ThemeTweaker\View\LayoutBlockInterface, \XLite\Base\IDecorator
{
    use ThemeTweaker\View\LayoutBlockTrait;

    /**
     * @return string
     */
    protected function getDefaultDisplayName()
    {
        return static::t('Product filter');
    }

    /**
     * Get current widget type parameter
     *
     * @return boolean
     */
    protected function getWidgetType()
    {
        return $this->getDisplayGroup() === static::DISPLAY_GROUP_SIDEBAR
            ? static::WIDGET_TYPE_SIDEBAR
            : static::WIDGET_TYPE_CENTER;
    }
}
