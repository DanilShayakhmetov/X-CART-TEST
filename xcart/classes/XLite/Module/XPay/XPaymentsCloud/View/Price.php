<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View;

/**
 * Product price
 */
class Price extends \XLite\View\Price implements \XLite\Base\IDecorator
{
    /**
     * Check if is need to show subscriptions info
     *
     * @return boolean
     */
    protected function isShowXpaymentsSubscriptionInfo()
    {
        return $this->hasXpaymentsSubscription();
    }

    /**
     * Check if product has subscription plan
     *
     * @return boolean
     */
    protected function hasXpaymentsSubscription()
    {
        return $this->getProduct()
            && $this->getProduct()->hasXpaymentsSubscriptionPlan();
    }

    /**
     * getSubscriptionPlan
     *
     * @return \XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Plan
     */
    protected function getXpaymentsSubscriptionPlan()
    {
        return $this->hasXpaymentsSubscription()
            ? $this->getProduct()->getXpaymentsSubscriptionPlan()
            : null;
    }

    /**
     * Get calculated Setup fee
     *
     * @return float|integer
     */
    protected function getXpaymentsSetupFee()
    {
        $fee = $this->hasXpaymentsSubscription()
            ? $this->getProduct()->getXpaymentsDisplayFeePrice()
            : 0;

        return $this->getListPrice() - $fee;
    }

    /**
     * getXpaymentsPlanDescription
     *
     * @return string
     */
    protected function getXpaymentsPlanDescription()
    {
        return $this->hasXpaymentsSubscription()
            ? $this->getXpaymentsSubscriptionPlan()->getLongDescription()
            : '';
    }

    /**
     * getXpaymentsTotalPaymentsDescription
     *
     * @return string
     */
    protected function getXpaymentsTotalPaymentsDescription()
    {
        return $this->hasXpaymentsSubscription()
            ? $this->getXpaymentsSubscriptionPlan()->getXpaymentsTotalPaymentsDescription()
            : '';
    }

}
