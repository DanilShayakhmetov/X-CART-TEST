<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Model;

use XLite\Module\XC\ThemeTweaker\Core\ThemeTweaker;

/**
 * View list decorator
 */
class ViewList extends \XLite\Model\ViewList implements \XLite\Base\IDecorator
{
    /**
     * Check if this view list item will be rendered
     *
     * @return boolean
     */
    public function isDisplayed()
    {
        return ThemeTweaker::getInstance()->isInLayoutMode()
            || parent::isDisplayed();
    }
}
