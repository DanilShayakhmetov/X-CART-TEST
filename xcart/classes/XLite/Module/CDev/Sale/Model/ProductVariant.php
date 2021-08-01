<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Model;

use XLite\View\FormField\Input\PriceOrPercent;
use XLite\View\FormField\Select\AbsoluteOrPercent;

/**
 * Product variant
 * @Decorator\Depend("XC\ProductVariants")
 *
 */
class ProductVariant extends \XLite\Module\XC\ProductVariants\Model\ProductVariant implements \XLite\Base\IDecorator
{
    /**
     * Sale discount type
     *
     * @var string
     *
     * @Column (type="string", length=32, nullable=false)
     */
    protected $discountType = \XLite\Model\Product::SALE_DISCOUNT_TYPE_PRICE;

    /**
     * "Sale value"
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $salePriceValue = 0;

    /**
     * Default sale flag
     *
     * @var boolean
     *
     * @Column (type="boolean", options={"default" : "1"})
     */
    protected $defaultSale = true;


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
     * Return old display product price (before sale)
     *
     * @return float
     */
    public function getDisplayPriceBeforeSale()
    {
        return \XLite\Module\CDev\Sale\Logic\PriceBeforeSale::getInstance()->apply($this, 'getNetPriceBeforeSale', array('taxable'), 'display');
    }

    /**
     * Get quick data price
     *
     * @return float
     */
    public function getQuickDataPrice()
    {
        $price = parent::getQuickDataPrice();

        if (!$this->getDefaultSale()) {
            if ($this->getDiscountType() === \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT) {
                $price = $price * (1 - $this->getSalePriceValue() / 100);

            } else {
                $price = $this->getSalePriceValue();
            }

        } elseif ($this->getProduct()->getParticipateSale()) {
            if ($this->getProduct()->getDiscountType() === \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT) {
                $price = $price * (1 - $this->getProduct()->getSalePriceValue() / 100);

            } else {
                $price = $this->getProduct()->getSalePriceValue();
            }
        }

        return $price;
    }

    /**
     * @return bool
     */
    public function getDefaultSale()
    {
        return $this->defaultSale;
    }

    /**
     * @param bool $defaultSale
     */
    public function setDefaultSale($defaultSale)
    {
        $this->defaultSale = $defaultSale;
    }

    /**
     * @return string
     */
    public function getDiscountType()
    {
        return $this->discountType ?: \XLite\Model\Product::SALE_DISCOUNT_TYPE_PRICE;
    }

    /**
     * @param string $saleDiscountType
     */
    public function setDiscountType($saleDiscountType)
    {
        $this->discountType = $saleDiscountType;
    }

    /**
     * @return float
     */
    public function getSalePriceValue()
    {
        return $this->salePriceValue;
    }

    /**
     * @param float $salePriceValue
     */
    public function setSalePriceValue($salePriceValue)
    {
        $this->salePriceValue = $salePriceValue;
    }

    /**
     * Returns sale field data
     *
     * @return array
     */
    public function getSale()
    {
        $value = $this->getDefaultSale() ? '' : $this->getSalePriceValue();
        $type = $this->getDefaultSale()
            ? (
                $this->getProduct() && $this->getProduct()->getParticipateSale()
                    ? $this->getProduct()->getDiscountType()
                    : \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT
            )
            : $this->getDiscountType();

        $sale = [
            PriceOrPercent::PRICE_VALUE => $value,
            PriceOrPercent::TYPE_VALUE  => $type === \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT
                ? AbsoluteOrPercent::TYPE_PERCENT
                : AbsoluteOrPercent::TYPE_ABSOLUTE
        ];

        return $sale;
    }

    /**
     * Set Sale
     *
     * @param array $sale
     * @return ProductVariant
     */
    public function setSale($sale)
    {
        $this->setSalePriceValue(
            isset($sale[PriceOrPercent::PRICE_VALUE])
                ? $sale[PriceOrPercent::PRICE_VALUE]
                : 0
        );

        $this->setDiscountType(
            isset($sale[PriceOrPercent::TYPE_VALUE])
            && $sale[PriceOrPercent::TYPE_VALUE] === AbsoluteOrPercent::TYPE_PERCENT
                ? \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT
                : \XLite\Model\Product::SALE_DISCOUNT_TYPE_PRICE
        );

        return $this;
    }


}
