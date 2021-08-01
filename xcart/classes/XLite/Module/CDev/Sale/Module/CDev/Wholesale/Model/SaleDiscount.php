<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Module\CDev\Wholesale\Model;

/**
 * Sale
 * @Decorator\Depend ("CDev\Wholesale")
 */
class SaleDiscount extends \XLite\Module\CDev\Sale\Model\SaleDiscount implements \XLite\Base\IDecorator
{
    /**
     * Flag: Sale is used for wholesale prices or not
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $applyToWholesale = false;

    /**
     * @return bool
     */
    public function getApplyToWholesale()
    {
        return $this->applyToWholesale;
    }

    /**
     * @param bool $applyToWholesale
     */
    public function setApplyToWholesale($applyToWholesale)
    {
        $this->applyToWholesale = $applyToWholesale;
    }
}
