<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Model;

/**
 * Wholesale price model (product variant)
 *
 * @Entity
 * @Table  (name="product_variant_wholesale_prices",
 *      indexes={
 *          @Index (name="range", columns={"product_variant_id", "membership_id", "quantityRangeBegin", "quantityRangeEnd"})
 *      }
 * )
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
class ProductVariantWholesalePrice extends \XLite\Module\CDev\Wholesale\Model\Base\AWholesalePrice
{
    /**
     * Relation to a product variant entity
     *
     * @var \XLite\Module\XC\ProductVariants\Model\ProductVariant
     *
     * @ManyToOne  (targetEntity="XLite\Module\XC\ProductVariants\Model\ProductVariant",cascade={"persist"})
     * @JoinColumn (name="product_variant_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $productVariant;

    /**
     * Return owner
     *
     * @return \XLite\Module\XC\ProductVariants\Model\ProductVariant
     */
    public function getOwner()
    {
        return $this->getProductVariant();
    }

    /**
     * Set owner
     *
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $owner Owner
     *
     * @return static
     */
    public function setOwner($owner)
    {
        return $this->setProductVariant($owner);
    }

    /**
     * @inheritdoc
     */
    public function getOwnerPrice()
    {
        if ($this->getOwner()) {
            return $this->getOwner()->getDefaultPrice()
                ? $this->getOwner()->getProduct()->getPrice()
                : $this->getOwner()->getPrice();
        } else {
            return null;
        }
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->getOwner() ? $this->getOwner()->getProduct() : null;
    }

    /**
     * Set product: fake method for compatibility with \XLite\Module\CDev\Wholesale\Model\WholesalePrice class
     *
     * @param \XLite\Model\Product $product
     *
     * @return static
     */
    public function setProduct($product)
    {
        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set productVariant
     *
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $productVariant
     * @return ProductVariantWholesalePrice
     */
    public function setProductVariant(\XLite\Module\XC\ProductVariants\Model\ProductVariant $productVariant = null)
    {
        $this->productVariant = $productVariant;
        return $this;
    }

    /**
     * Get productVariant
     *
     * @return \XLite\Module\XC\ProductVariants\Model\ProductVariant 
     */
    public function getProductVariant()
    {
        return $this->productVariant;
    }
}
