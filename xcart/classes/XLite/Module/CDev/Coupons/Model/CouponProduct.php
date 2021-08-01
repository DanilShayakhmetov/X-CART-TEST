<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Model;

/**
 * Category
 *
 * @Entity
 * @Table (name="coupon_products",
 *      uniqueConstraints={
 *          @UniqueConstraint (name="pair", columns={"coupon_id","product_id"})
 *      },
 * )
 */
class CouponProduct extends \XLite\Model\AEntity
{
    /**
     * Primary key
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Relation to a coupon entity
     *
     * @var \XLite\Module\CDev\Coupons\Model\Coupon
     *
     * @ManyToOne  (targetEntity="XLite\Module\CDev\Coupons\Model\Coupon", inversedBy="couponProducts")
     * @JoinColumn (name="coupon_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $coupon;

    /**
     * Relation to a product entity
     *
     * @var \XLite\Model\Product
     *
     * @ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="couponProducts")
     * @JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Coupon
     */
    public function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * @param Coupon $coupon
     */
    public function setCoupon($coupon)
    {
        $this->coupon = $coupon;
    }

    /**
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param \XLite\Model\Product $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->getProduct()->getName();
    }

    /**
     * @return float
     */
    public function getProductPrice()
    {
        return $this->getProduct()->getPrice();
    }

    /**
     * @return string
     */
    public function getProductSku()
    {
        return $this->getProduct()->getSku();
    }

    /**
     * @return int
     */
    public function getProductAmount()
    {
        return $this->getProduct()->getAmount();
    }
}
