<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Model\Repo;

/**
 * Cart repository extension
 */
class Cart extends \XLite\Model\Repo\Cart implements \XLite\Base\IDecorator
{
    /**
     * We need to alter findOneByProfile() method to skip Buy With Apple Pay carts
     * but CDev/XPaymentsConnector and XC/MultiVendor are also modifying that method
     * so to avoid decoration of decorators with all combinations we go one level
     * deeper and alter the findOneBy method directly (which is also used by Doctrine
     * to parse magic call for default findOneByProfile()
     *
     * @param \XLite\Model\Profile $profile Profile object
     *
     * @return \XLite\Model\Cart
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        if (array_key_exists('profile', $criteria)) {
            $criteria['buyWithApplePay'] = false;
        }

        return parent::findOneBy($criteria, $orderBy);
    }
}
