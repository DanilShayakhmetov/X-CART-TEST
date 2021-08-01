<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\View\FormField\Inline\Input\Checkbox\Switcher;

/**
 * Switcher
 */
class FreePaid extends \XLite\View\FormField\Inline\Input\Checkbox\Switcher\OnOff
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\Module\CDev\Egoods\View\FormField\Input\Checkbox\FreePaid';
    }
}