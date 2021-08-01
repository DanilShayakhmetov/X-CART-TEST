<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Label;


/**
 * ReplyToAdmin
 */
class ReplyToAdmin extends \XLite\View\FormField\Label
{
    protected function getLabelValue()
    {
        return static::t('Customer email (if exist)');
    }
}