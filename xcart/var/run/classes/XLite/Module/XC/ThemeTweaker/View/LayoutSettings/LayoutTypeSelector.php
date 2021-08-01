<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\LayoutSettings;

use \XLite\Core\Layout;

/**
 * Layout settings
 */
 class LayoutTypeSelector extends \XLite\View\LayoutSettings\LayoutTypeSelectorAbstract implements \XLite\Base\IDecorator
{
    /**
     * @return bool
     */
    protected function isVisible()
    {
        return false;
    }
}
