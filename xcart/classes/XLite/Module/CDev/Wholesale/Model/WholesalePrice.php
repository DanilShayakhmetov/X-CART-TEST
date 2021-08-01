<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Model;

/**
 * Wholesale price model (product)
 *
 * @Entity
 * @Table  (name="wholesale_prices",
 *      indexes={
 *          @Index (name="range", columns={"product_id", "membership_id", "quantityRangeBegin", "quantityRangeEnd"})
 *      }
 * )
 */
class WholesalePrice extends \XLite\Module\CDev\Wholesale\Model\Base\AWholesalePrice
{
    /**
     * Relation to a product entity
     *
     * @var \XLite\Model\Product
     *
     * @ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="wholesalePrices")
     * @JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Return owner
     *
     * @return \XLite\Model\Product
     */
    public function getOwner()
    {
        return $this->getProduct();
    }

    /**
     * Set owner
     *
     * @param \XLite\Model\Product $owner Owner
     *
     * @return static
     */
    public function setOwner($owner)
    {
        return $this->setProduct($owner);
    }

    /**
     * @inheritdoc
     */
    public function getOwnerPrice()
    {
        return $this->getOwner()
            ? $this->getOwner()->getPrice()
            : null;
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
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return static
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }
}
