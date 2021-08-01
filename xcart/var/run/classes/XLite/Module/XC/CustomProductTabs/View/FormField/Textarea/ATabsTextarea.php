<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\View\FormField\Textarea;

/**
 * Abstract custom tabs textarea
 */
abstract class ATabsTextarea extends \XLite\View\FormField\Textarea\Advanced
{
    /**
     * Get default wrapper class
     *
     * @return string
     */
    protected function getDefaultWrapperClass()
    {
        $class = parent::getDefaultWrapperClass();

        return $class . ' textarea-advanced';
    }
}