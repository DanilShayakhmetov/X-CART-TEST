<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Model;

/**
 * Used coupon
 *
 *  Entity
 *  Table  (name="order_coupons")
 * @HasLifecycleCallbacks
 */
abstract class UsedCouponAbstract extends \XLite\Model\AEntity
{
    /**
     * Product unique ID
     *
     * @var   integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Code
     *
     * @var   string
     *
     * @Column (type="string", options={ "fixed": true }, length=16)
     */
    protected $code;

    /**
     * Value
     *
     * @var   float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $value = 0.0000;

    /**
     * Order
     *
     * @var   \XLite\Model\Order
     *
     * @ManyToOne  (targetEntity="XLite\Model\Order", inversedBy="usedCoupons")
     * @JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * Coupon
     *
     * @var   \XLite\Module\CDev\Coupons\Model\Coupon
     *
     * @ManyToOne  (targetEntity="XLite\Module\CDev\Coupons\Model\Coupon", inversedBy="usedCoupons")
     * @JoinColumn (name="coupon_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $coupon;

    /**
     * Type
     *
     * @var   string
     *
     * @Column (type="string", options={ "fixed": true }, length=1, nullable=true)
     */
    protected $type;

    // {{{ Getters / setters

    /**
     * setCoupon
     *
     * @param \XLite\Module\CDev\Coupons\Model\Coupon $coupon ____param_comment____
     *
     * @return void
     */
    public function setCoupon(\XLite\Module\CDev\Coupons\Model\Coupon $coupon)
    {
        $this->coupon = $coupon;
        $this->setCode($coupon->getCode());
    }

    /**
     * Get public code (masked)
     *
     * @return string
     */
    public function getPublicCode()
    {
        return $this->getActualCode();
    }

    /**
     * Get coupon public name
     *
     * @return string
     */
    public function getPublicName()
    {
        $suffix = '';

        if (
            $this->getType()
            && $this->getType() === \XLite\Module\CDev\Coupons\Model\Coupon::TYPE_PERCENT
            && $this->getOrder()
        ) {
            $percent = round($this->getValue() / $this->getOrder()->getSubtotal() * 100, 2);

            $suffix = sprintf('(%s%%)', $percent);
        } elseif ($this->getCoupon()) {
            return $this->getCoupon()->getPublicName();
        }


        return $this->getPublicCode() . ' ' . $suffix;
    }

    /**
     * Get actual code
     *
     * @return string
     */
    public function getActualCode()
    {
        return $this->getCoupon() ? $this->getCoupon()->getCode() : $this->getCode();
    }

    /**
     * Check - coupon deleted or not
     *
     * @return boolean
     */
    public function isDeleted()
    {
        return !(bool)$this->getCoupon();
    }

    // }}}

    // {{{ Processing

    /**
     * Mark as used
     *
     * @return void
     */
    public function markAsUsed()
    {
        if ($this->getCoupon()) {
            $this->getCoupon()->setUses($this->getCoupon()->getUses() + 1);
        }
    }

    /**
     * Unmark as used
     *
     * @return void
     */
    public function unmarkAsUsed()
    {
        if ($this->getCoupon() && 0 < $this->getCoupon()->getUses()) {
            $this->getCoupon()->setUses($this->getCoupon()->getUses() - 1);
        }
    }

    // }}}

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
     * Set code
     *
     * @param string $code
     *
     * @return UsedCoupon
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set value
     *
     * @param float $value
     *
     * @return UsedCoupon
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set order
     *
     * @param \XLite\Model\Order $order
     *
     * @return UsedCoupon
     */
    public function setOrder(\XLite\Model\Order $order = null)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Get coupon
     *
     * @return \XLite\Module\CDev\Coupons\Model\Coupon
     */
    public function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * Return Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set Type
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Remove
     *
     * @PreRemove
     */
    public function processRemove()
    {
        if ($this->getCoupon() && $this->getCoupon()->getUsedCoupons()) {
            $this->getCoupon()->getUsedCoupons()->removeElement($this);
        }

        if ($this->getOrder() && $this->getOrder()->getUsedCoupons()) {
            $this->getOrder()->getUsedCoupons()->removeElement($this);
        }
    }
}
