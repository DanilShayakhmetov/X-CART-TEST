<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Model;

/**
 * CleanURL
 */
 class CleanURL extends \XLite\Module\CDev\SimpleCMS\Model\CleanURL implements \XLite\Base\IDecorator
{
    /**
     * Relation to a product entity
     *
     * @var \XLite\Module\CDev\Sale\Model\SaleDiscount
     *
     * @ManyToOne  (targetEntity="XLite\Module\CDev\Sale\Model\SaleDiscount", inversedBy="cleanURLs")
     * @JoinColumn (name="sale_discount_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $sale_discount;

    /**
     * Set page
     *
     * @param \XLite\Module\CDev\Sale\Model\SaleDiscount $saleDiscount
     * @return CleanURL
     */
    public function setSaleDiscount(\XLite\Module\CDev\Sale\Model\SaleDiscount $saleDiscount = null)
    {
        $this->sale_discount = $saleDiscount;
        return $this;
    }

    /**
     * @return SaleDiscount
     */
    public function getSaleDiscount()
    {
        return $this->sale_discount;
    }
}
