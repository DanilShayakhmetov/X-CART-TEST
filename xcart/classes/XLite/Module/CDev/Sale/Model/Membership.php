<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Model;

/**
 * Membership
 */
abstract class Membership extends \XLite\Model\Membership implements \XLite\Base\IDecorator
{
    /**
     * Sale discounts
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToMany (targetEntity="XLite\Module\CDev\Sale\Model\SaleDiscount", mappedBy="memberships")
     */
    protected $saleDiscounts;

    /**
     * Add sale discount
     *
     * @param \XLite\Module\CDev\Sale\Model\SaleDiscount $saleDiscount
     */
    public function addSaleDiscount(\XLite\Module\CDev\Sale\Model\SaleDiscount $saleDiscount)
    {
        $this->saleDiscounts[] = $saleDiscount;
    }

    /**
     * Get sale discount
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSaleDiscounts()
    {
        return $this->saleDiscounts;
    }
}
