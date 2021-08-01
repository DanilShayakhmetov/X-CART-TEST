<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View\ItemsList\Product\Customer;

/**
 * Sale discount products list
 *
 * @Decorator\Depend ("CDev\Sale")
 */
class SaleDiscount extends \XLite\Module\CDev\Sale\View\ItemsList\Product\Customer\SaleDiscount implements \XLite\Base\IDecorator
{
    /**
     * Register Meta tags
     *
     * @return array
     */
    public function getMetaTags()
    {
        $list = parent::getMetaTags();

        if ($this->getSaleDiscount()) {
            $list[] = $this->getSaleDiscount()->getOpenGraphMetaTags();
        }

        return $list;
    }
}
