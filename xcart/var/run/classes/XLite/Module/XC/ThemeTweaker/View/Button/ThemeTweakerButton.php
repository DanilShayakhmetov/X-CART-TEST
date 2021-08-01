<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\Button;

/**
 * Simple button
 */
class ThemeTweakerButton extends \XLite\View\Button\Simple
{
    /**
     * getDefaultStyle
     *
     * @return string
     */
    protected function getDefaultButtonClass()
    {
        return '';
    }

    /**
     * Define the button type (btn-warning and so on)
     *
     * @return string
     */
    protected function getDefaultButtonType()
    {
        return 'themetweaker-button';
    }
}
