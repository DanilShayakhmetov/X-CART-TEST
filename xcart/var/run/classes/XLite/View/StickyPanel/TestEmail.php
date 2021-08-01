<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel;

/**
 * Test email sticky panel
 */
class TestEmail extends \XLite\View\StickyPanel\ItemForm
{
    /**
     * Defines the label for the save button
     *
     * @return string
     */
    protected function getSaveWidgetLabel()
    {
        return static::t('Send test email');
    }
}
