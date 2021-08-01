<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Model\Subscription;

/**
 * X-Payments Subscription Plan entity
 *
 * @Entity
 * @Table  (name="xpayments_subscription_plans")
 */
class Plan extends Base\ASubscriptionPlan
{
    /**
     * Unique id
     *
     * @var int
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true, "comment": "Unique id" })
     */
    protected $id;

    /**
     * Product
     *
     * @var \XLite\Model\Product
     *
     * @OneToOne   (targetEntity="XLite\Model\Product", inversedBy="xpaymentsSubscriptionPlan")
     * @JoinColumn (name="productId", referencedColumnName="product_id")
     */
    protected $product;

    /**
     * Is the product a subscription plan
     *
     * @var boolean
     *
     * @Column (type="boolean", options={ "comment": "Is the product a subscription plan" })
     */
    protected $isSubscription = false;

    /**
     * Setup fee for plan
     *
     * @var float
     *
     * @Column (type="money", options={ "comment": "Setup fee for the plan" })
     */
    protected $setupFee = 0.0000;

    /**
     * Whether to calculate shipping for recurring orders
     *
     * @var boolean
     *
     * @Column (type="boolean", options={ "comment": "Whether to calculate shipping for recurring orders" })
     */
    protected $calculateShipping = false;

    /**
     * Set subscription
     *
     * @param boolean $isSubscription
     *
     * @return Plan
     */
    public function setIsSubscription($isSubscription)
    {
        $this->isSubscription = $isSubscription;
        return $this;
    }

    /**
     * Is subscription
     *
     * @return boolean
     */
    public function getIsSubscription()
    {
        return $this->isSubscription;
    }

    /**
     * Set setupFee
     *
     * @param float $setupFee
     *
     * @return Plan
     */
    public function setSetupFee($setupFee)
    {
        $this->setupFee = $setupFee;
        return $this;
    }

    /**
     * Get setupFee
     *
     * @return float
     */
    public function getSetupFee()
    {
        return $this->setupFee;
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
     * Set type
     *
     * @param string $type
     *
     * @return Plan
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set number
     *
     * @param integer $number
     *
     * @return Plan
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Set period
     *
     * @param string $period
     *
     * @return Plan
     */
    public function setPeriod($period)
    {
        $this->period = $period;
        return $this;
    }

    /**
     * Get period
     *
     * @return string
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Set reverse
     *
     * @param boolean $reverse
     *
     * @return Plan
     */
    public function setReverse($reverse)
    {
        $this->reverse = $reverse;
        return $this;
    }

    /**
     * Get reverse
     *
     * @return boolean
     */
    public function getReverse()
    {
        return $this->reverse;
    }

    /**
     * Set periods
     *
     * @param integer $periods
     *
     * @return Plan
     */
    public function setPeriods($periods)
    {
        $this->periods = $periods;
        return $this;
    }

    /**
     * Get periods
     *
     * @return integer
     */
    public function getPeriods()
    {
        return $this->periods;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     *
     * @return Plan
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

    /**
     * Set value of "Calculate shipping for recurring orders" option of subscription plan
     *
     * @param boolean $calculateShipping
     *
     * @return Plan
     */
    public function setCalculateShipping($calculateShipping)
    {
        $this->calculateShipping = $calculateShipping;
        return $this;
    }

    /**
     * Get value of "Calculate shipping for recurring orders" option of subscription plan
     *
     * @return string
     */
    public function getCalculateShipping()
    {
        return $this->calculateShipping;
    }

}
