<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Pager;


class Conversations extends \XLite\View\Pager\Customer\ACustomer
{
    /**
     * @inheritdoc
     */
    protected function getItemsPerPageMin()
    {
        return 10;
    }

    /**
     * isItemsPerPageVisible
     *
     * @return boolean
     */
    protected function isItemsPerPageVisible()
    {
        return false;
    }
}