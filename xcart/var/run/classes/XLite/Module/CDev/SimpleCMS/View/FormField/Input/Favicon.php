<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\View\FormField\Input;

/**
 * Favicon
 */
class Favicon extends \XLite\Module\CDev\SimpleCMS\View\FormField\Input\AImage
{

    /**
     * @return boolean
     */
    protected function isViaUrlAllowed() {
        return false;
    }

    /**
     * @return string
     */
    protected function allowExtendedTypes()
    {
        return true;
    }
}
