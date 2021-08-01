<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Module\XC\ProductVariants\Model;

/**
 * Product
 *
 * @Decorator\Depend({"CDev\Sale","XC\ProductVariants"})
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    protected $onSaleVariantsCount;

    protected function getOnSaleVariantsCount()
    {
        if (!isset($this->onSaleVariantsCount)) {
            $this->onSaleVariantsCount = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')
                ->getOnSaleVariantsCountByProduct($this);
        }

        return $this->onSaleVariantsCount;
    }

    /**
     * Check if product has sales
     *
     * @return bool
     */
    public function hasParticipateSale()
    {
        return parent::hasParticipateSale()
            || 0 < $this->getOnSaleVariantsCount();
    }
}
