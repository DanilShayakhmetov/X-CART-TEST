<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Model;

use XLite\Module\XPay\XPaymentsCloud\Main as XPaymentsCloud;
use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription;

/**
 * Fake order item for zero auth's and recharges from X-Payments.
 * Also, this class declares additional fields for subscriptions.
 * Something customer can not put into his cart
 */
class OrderItem extends \XLite\Model\OrderItem implements \XLite\Base\IDecorator
{
    /**
     * Subscription
     *
     * @var Subscription
     *
     * @OneToOne (targetEntity="XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription", mappedBy="initialOrderItem", cascade={"all"})
     * @JoinColumn (name="subscriptionId", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $xpaymentsSubscription;

    /**
     * Flag for zero auth and recharges
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $xpaymentsEmulated = false;

    /**
     * Unique id for mapping of X-Payments subscriptions with X-Cart order items
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $xpaymentsUniqueId = '';

    /**
     * Is this item a fake one for zero auth and recharges
     *
     * @return boolean
     */
    public function isXpaymentsEmulated()
    {
        return $this->getXpaymentsEmulated();
    }

    /**
     * Check if item is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->isXpaymentsEmulated()
            || parent::isValid();
    }

    /**
     * Deleted Item flag
     *
     * @return boolean
     */
    public function isDeleted()
    {
        $result = parent::isDeleted();

        if ($this->isXpaymentsEmulated()) {
            $result = false;
        }

        return $result;
    }

    /**
     * Returns deleted product for fake items
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        if ($this->isXpaymentsEmulated()) {
            return $this->getDeletedProduct();
        } else {
            return parent::getProduct();
        }
    }

    /**
     * Returns deleted product for fake items
     *
     * @return \XLite\Model\Product
     */
    public function getObject()
    {
        if ($this->isXpaymentsEmulated()) {
            return $this->getDeletedProduct();
        } else {
            return parent::getObject();
        }
    }

    /**
     * Check if the item is valid to clone through the Re-order functionality
     *
     * @return boolean
     */
    public function isValidToClone()
    {
        if ($this->isXpaymentsEmulated()) {

            $result = false;

        } else {

            $result = parent::isValidToClone();
        }

        return $result;
    }

    /**
     * Set xpaymentsEmulated
     *
     * @param boolean $xpaymentsEmulated
     * @return OrderItem
     */
    public function setXpaymentsEmulated($xpaymentsEmulated)
    {
        $this->xpaymentsEmulated = $xpaymentsEmulated;
        return $this;
    }

    /**
     * Get xpaymentsEmulated
     *
     * @return boolean
     */
    public function getXpaymentsEmulated()
    {
        return $this->xpaymentsEmulated;
    }

    /**
    * Get item clear price. This value is used as a base item price for calculation of netPrice
    *
    * @return float
    */
    public function getClearPrice()
    {
        if ($this->isXpaymentsEmulated()) {
            return parent::getPrice();
        } else {
            return parent::getClearPrice();
        }
    }

    /**
     * Check if order item is subscription
     *
     * @return bool
     */
    public function isXpaymentsSubscription()
    {
        $isSubscription = false;

        if (!is_null($this->getXpaymentsSubscription())) {
            $isSubscription = true;

        } elseif (!is_null($this->getProduct())) {
            $isSubscription = $this->getProduct()->hasXpaymentsSubscriptionPlan();
        }

        return $isSubscription;
    }

    /**
     * Get net price
     * Override magic method (see $price field annotation)
     *
     * @return float
     */
    public function getNetPrice()
    {
        return ($this->getXpaymentsSubscription() && $this->getXpaymentsSubscription()->getInitialOrderItem() !== $this)
            ? $this->getXpaymentsNetFeePrice()
            : parent::getNetPrice();
    }

    /**
     * isInitialSubscription
     *
     * @return boolean
     */
    public function isInitialXpaymentsSubscription()
    {
        return $this->isXpaymentsSubscription()
            && $this->getXpaymentsSubscription()
            && $this->getItemId() === $this->getXpaymentsSubscription()->getInitialOrderItem()->getItemId();
    }

    /**
     * Get setup fee
     *
     * @return float
     */
    public function getXpaymentsSetupFee()
    {
        return $this->isInitialXpaymentsSubscription()
            ? $this->getDisplayPrice() - $this->getXpaymentsDisplayFeePrice()
            : 0;
    }

    /**
     * Get subscription fee
     *
     * @return float
     */
    public function getXpaymentsSubscriptionFee()
    {
        return $this->isXpaymentsSubscription() && $this->getXpaymentsSubscription()
            ? $this->getXpaymentsSubscription()->getFee()
            : 0;
    }

    /**
     * Set subscription
     *
     * @param Subscription $xpaymentsSubscription
     * @return OrderItem
     */
    public function setXpaymentsSubscription(Subscription $xpaymentsSubscription = null)
    {
        $this->xpaymentsSubscription = $xpaymentsSubscription;
        return $this;
    }

    /**
     * Get subscription
     *
     * @return Subscription
     */
    public function getXpaymentsSubscription()
    {
        return $this->xpaymentsSubscription;
    }

    /**
     * Get clear fee price
     *
     * @return float
     */
    public function getXpaymentsClearFeePrice()
    {
        return $this->getProduct()->getXpaymentsClearFeePrice();
    }

    /**
     * Get net fee Price
     *
     * @return float
     */
    public function getXpaymentsNetFeePrice()
    {
        return \XLite\Logic\Price::getInstance()->apply($this, 'getXpaymentsClearFeePrice', ['taxable'], 'xpaymentsNetFee');
    }

    /**
     * Get display fee Price
     *
     * @return float
     */
    public function getXpaymentsDisplayFeePrice()
    {
        return \XLite\Logic\Price::getInstance()->apply($this, 'getXpaymentsNetFeePrice', ['taxable'], 'xpaymentsDisplayFee');
    }

    /**
     * Get unique id for mapping of X-Payments subscriptions with X-Cart order items
     *
     * @param $xpaymentsUniqueId
     *
     * @return $this
     */
    public function setXpaymentsUniqueId($xpaymentsUniqueId)
    {
        $this->xpaymentsUniqueId = $xpaymentsUniqueId;
        return $this;
    }

    /**
     * Set unique id for mapping of X-Payments subscriptions with X-Cart order items
     *
     * @return string
     */
    public function getXpaymentsUniqueId()
    {
        return $this->xpaymentsUniqueId;
    }

    /**
     * Is next payment date available for current order
     *
     * @return bool
     */
    public function isXpaymentsNextPaymentDateAvailable()
    {
        $xpaymentsSubscription = $this->getXpaymentsSubscription();

        return $xpaymentsSubscription
            && Subscription::STATUS_ACTIVE === $xpaymentsSubscription->getStatus()
            && (
                !$xpaymentsSubscription->getLastOrderId()
                || $xpaymentsSubscription->getLastOrderId() == $this->getOrder()->getOrderId()
            );
    }

}
