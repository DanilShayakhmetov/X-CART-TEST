<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Model;

use Includes\Utils\Module\Manager;

/**
 * Product variant
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
class ProductVariant extends \XLite\Module\XC\ProductVariants\Model\ProductVariant implements \XLite\Base\IDecorator
{
    /**
     * Is current selected variant has Tier 1 Wholesale price
     * @return bool
     */
    public function isFirstWholesaleTier()
    {
        $wholesaleprices = \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\ProductVariantWholesalePrice')->getWholesalePrices(
            $this,
            $this->getProduct()->getCurrentMembership()
        );

        return empty($wholesaleprices) || $wholesaleprices[0]->getQuantityRangeBegin() > $this->getProduct()->getWholesaleQuantity();
    }

    /**
     * Check if wholesale prices are enabled for the specified product.
     *
     * @return boolean
     */
    public function isWholesalePricesEnabled()
    {
        $result = true;

        if (Manager::getRegistry()->isModuleEnabled('CDev-Sale')) {
            $result = $this->getDefaultSale()
                ? !$this->getProduct()->getParticipateSale() || $this->getProduct()->getDiscountType() === \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT
                : $this->getDiscountType() === \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT;
        }

        return $result
            && (
                !$this->getDefaultPrice()
                || $this->getProduct()->isWholesalePricesEnabled()
            );
    }

    /**
     * Get minimum product quantity available to customer to purchase
     *
     * @param \XLite\Model\Membership $membership Customer's membership OPTIONAL
     *
     * @return integer
     */
    public function getMinQuantity($membership = null)
    {
        return $this->getProduct()->getMinQuantity($membership);
    }

    /**
     * Override clear price
     *
     * @return float
     */
    public function getClearPrice()
    {
        $price = parent::getClearPrice();

        if (
            $this->isWholesalePricesEnabled()
            && $this->isPersistent()
         ) {
            $membership = $this->getProduct()->getCurrentMembership();
            $wholesalePrice = $this->getDefaultPrice()
                ? $this->getProduct()->getWholesalePrice($membership)
                : \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\ProductVariantWholesalePrice')->getPrice(
                    $this,
                    $this->getProduct()->getWholesaleQuantity() ?: $this->getProduct()->getMinQuantity($membership),
                    $membership
                );

            if (!is_null($wholesalePrice)) {
                $price = $wholesalePrice;
            }
        }

        return $price;
    }

    /**
     * Return base price
     *
     * @return float
     */
    public function getBasePrice()
    {
        return parent::getClearPrice();
    }

    /**
     * Clone
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $newEntity = parent::cloneEntity();

        $this->cloneMembership($newEntity);

        return $newEntity;
    }

    /**
     * Clone membership (used in cloneEntity() method)
     *
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $newEntity
     *
     * @return void
     */
    protected function cloneMembership(\XLite\Module\XC\ProductVariants\Model\ProductVariant $newEntity)
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\ProductVariantWholesalePrice');
        $em = \XLite\Core\Database::getEM();

        foreach ($repo->findBy(['productVariant' => $this]) as $price) {
            /** @var \XLite\Module\CDev\Wholesale\Model\ProductVariantWholesalePrice $price */
            $newPrice = $price->cloneEntity();
            $newPrice->setProductVariant($newEntity);
            $newPrice->setMembership($price->getMembership());
            $em->persist($newPrice);
        }
    }
}
