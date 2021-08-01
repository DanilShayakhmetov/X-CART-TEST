<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\MarketPrice\Model;

use XLite\Core\Converter;

/**
 * Product 
 */
 class Product extends \XLite\Module\CDev\MarketPrice\Module\CDev\Sale\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Product market price
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $marketPrice = 0.0000;

    /**
     * Set marketPrice
     *
     * @param float $marketPrice
     * @return Product
     */
    public function setMarketPrice($marketPrice)
    {
        $this->marketPrice = Converter::toUnsigned32BitFloat($marketPrice);
        return $this;
    }

    /**
     * Get marketPrice
     *
     * @return float 
     */
    public function getMarketPrice()
    {
        return $this->marketPrice;
    }

    /**
     * @return float
     */
    public function getNetMarketPrice()
    {
        return $this->getMarketPrice();
    }

    /**
     * @return float
     */
    public function getDisplayMarketPrice()
    {
        return $this->getMarketPrice();
    }
}
