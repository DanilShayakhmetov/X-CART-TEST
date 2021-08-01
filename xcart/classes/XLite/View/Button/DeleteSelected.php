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
 * 'Delete selected' button
 */
class DeleteSelected extends \XLite\View\Button\Regular
{
    use ConfirmableTrait, TooltippedTrait;

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Delete selected';
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultTitle()
    {
        return static::t('Delete selected');
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'delete';
    }

    /**
     * getDefaultConfirmationText
     *
     * @return string
     */
    protected function getDefaultConfirmationText()
    {
        return 'Do you really want to delete selected items?';
    }
}
