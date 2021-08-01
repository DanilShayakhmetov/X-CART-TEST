<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Module\CDev\Sale\View\ItemsList;

/**
 * Wholesale prices items list
 *
 * @Decorator\Depend("CDev\Sale")
 */
class WholesalePrices extends \XLite\Module\CDev\Wholesale\View\ItemsList\WholesalePrices implements \XLite\Base\IDecorator
{
    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !$this->isOnAbsoluteSale();
    }

    /**
     * @return bool
     */
    protected function isOnAbsoluteSale()
    {
        return $this->getProduct()->getParticipateSale()
            && $this->getProduct()->getDiscountType() === \XLite\Model\Product::SALE_DISCOUNT_TYPE_PRICE;
    }
}
