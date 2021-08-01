<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Module\CDev\Sale\Model\Base;

/**
 * Wholesale price model (abstract)
 *
 * @MappedSuperclass
 * @Decorator\Depend("CDev\Sale")
 */
abstract class AWholesalePrice extends \XLite\Module\CDev\Wholesale\Model\Base\AWholesalePrice implements \XLite\Base\IDecorator
{
    /**
     * Return old net product price (before sale)
     *
     * @return float
     */
    public function getNetPriceBeforeSale()
    {
        return \XLite\Module\CDev\Sale\Logic\PriceBeforeSale::getInstance()->apply($this, 'getClearPrice', array('taxable'), 'net');
    }

    /**
     * Get clear display Price
     *
     * @return float
     */
    public function getDisplayPriceBeforeSale()
    {
        return \XLite\Module\CDev\Sale\Logic\PriceBeforeSale::getInstance()->apply($this, 'getNetPriceBeforeSale', array('taxable'), 'display');
    }

    /**
     * Get clear display Price
     *
     * @return float
     */
    public function getClearDisplayPrice()
    {
        return $this->getDisplayPriceBeforeSale();
    }
}
