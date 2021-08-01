<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\Module\XC\Reviews\View\Pager\Customer;


/**
 * ACustomer
 *
 * @Decorator\Depend("XC\Reviews")
 */
 class ACustomer extends \XLite\Module\XC\Reviews\View\Pager\Customer\ACustomerAbstract implements \XLite\Base\IDecorator
{
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