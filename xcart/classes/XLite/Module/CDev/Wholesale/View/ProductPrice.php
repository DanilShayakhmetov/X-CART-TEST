<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View;

use XLite\Core\Auth;
use XLite\Core\Database;

/**
 * Wholesale prices for product
 */
class ProductPrice extends \XLite\View\Product\Details\Customer\Widget
{
    /**
     * Cache for wholesale prices array
     *
     * @var   array
     */
    protected $wholesalePrices = null;

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/Wholesale/product_price/style.css';

        return $list;
    }

    /**
     * Return the specific widget service name to make it visible as specific CSS class
     *
     * @return null|string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-product-wholesale-prices';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Wholesale/product_price/body.twig';
    }

    /**
     * Define wholesale prices
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    protected function defineWholesalePrices()
    {
        return Database::getRepo('XLite\Module\CDev\Wholesale\Model\WholesalePrice')->getWholesalePrices(
            $this->getProduct(),
            $this->getCart()->getProfile()
                ? $this->getCart()->getProfile()->getMembership()
                : Auth::getInstance()->getMembership()
        );
    }

    /**
     * @param \XLite\Module\CDev\Wholesale\Model\Base\AWholesalePrice[] $wholesalePrices
     *
     * @return array
     */
    protected function prepareWholesalePrices($wholesalePrices)
    {
        $result = [];
        $product = $this->getProduct();

        $minQty = $this->getProduct()->getMinQuantity(
            $this->getCart()->getProfile()
                ? $this->getCart()->getProfile()->getMembership()
                : null
        );
        $product = $this->getProduct();

        if (
            $wholesalePrices
            && isset($wholesalePrices[0])
            && $minQty < $wholesalePrices[0]->getQuantityRangeBegin()
        ) {
            $result[] = $this->prepareZeroTier($wholesalePrices[0], $minQty);
        }

        foreach ($wholesalePrices as $wholesalePrice) {
            $tier = new \XLite\Module\CDev\Wholesale\Model\DTO\WholesalePrice();
            $tier->init($wholesalePrice);

            $wholesaleQuantity = $product->getWholesaleQuantity();
            $product->setWholesaleQuantity($tier['quantityRangeBegin']);
            $attributesShift = \XLite\Logic\AttributeSurcharge::modifyMoney(0, $this->getProduct(), '', [], '');
            $product->setWholesaleQuantity($wholesaleQuantity);

            $tier['displayPrice'] += $attributesShift;

            $result[] = $tier;
        }

        return $result;
    }

    /**
     * @param $firstWholesalePrice
     * @param $minQty
     * @return \XLite\Module\CDev\Wholesale\Model\DTO\WholesalePrice
     */
    protected function prepareZeroTier($firstWholesalePrice, $minQty)
    {
        $zeroTier = new \XLite\Module\CDev\Wholesale\Model\DTO\WholesalePrice();

        $product = $this->getProduct();
        $wholesaleQuantity = $product->getWholesaleQuantity();
        $product->setWholesaleQuantity($minQty);
        $zeroTier['displayPrice'] = $this->getZeroTierDisplayPrice($firstWholesalePrice);
        $product->setWholesaleQuantity($wholesaleQuantity);

        $zeroTier['quantityRangeBegin'] = $minQty;
        $zeroTier['quantityRangeEnd'] = $firstWholesalePrice->getQuantityRangeBegin() - 1;

        return $zeroTier;
    }

    /**
     * @param $firstWholesalePrice
     * @return mixed
     */
    protected function getZeroTierDisplayPrice($firstWholesalePrice)
    {
        return $firstWholesalePrice->getOwner()->getDisplayPrice();
    }

    /**
     * Return wholesale prices for the current product
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    protected function getWholesalePrices()
    {
        if (!isset($this->wholesalePrices)) {
            $this->wholesalePrices = $this->prepareWholesalePrices(
                $this->defineWholesalePrices()
            );
        }

        return $this->wholesalePrices;
    }

    /**
     * @return boolean
     */
    protected function isWholesalePricesEnabled()
    {
        return $this->getProduct()->isWholesalePricesEnabled();
    }
}
