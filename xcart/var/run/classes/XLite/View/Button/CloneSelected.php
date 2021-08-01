<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

use XLite\View\Button\Features\ConfirmableTrait;
use XLite\View\Button\Features\TooltippedTrait;

/**
 * Clone selected button
 */
class CloneSelected extends \XLite\View\Button\Regular
{
    use ConfirmableTrait, TooltippedTrait;

    /**
     * Return default button label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Clone selected';
    }

    /**
     * Return default button title
     *
     * @return string
     */
    protected function getDefaultTitle()
    {
        return static::t('Clone selected');
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'clone';
    }

    /**
     * getDefaultConfirmationText
     *
     * @return string
     */
    protected function getDefaultConfirmationText()
    {
        return 'Do you really want to clone the selected items?';
    }
}
