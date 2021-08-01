<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Controller\Customer;


/**
 * Class Cart
 *
 * @Decorator\Depend({"XC\ProductVariants", "XC\FacebookMarketing"})
 */
class CartVariants extends \XLite\Controller\Customer\Cart implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Model\OrderItem $item
     *
     * @return integer
     */
    protected function getItemId($item)
    {
        if ($item->getVariant()) {
            return $item->getVariant()->getSku() ?: $item->getVariant()->getVariantId();
        }

        return parent::getItemId($item);
    }
}