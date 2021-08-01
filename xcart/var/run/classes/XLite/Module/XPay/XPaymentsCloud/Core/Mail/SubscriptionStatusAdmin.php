<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Core\Mail;

use XLite\Model\Order;
use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription;
use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Plan;
use XLite\View\AView;
use XLite\Core\Converter;
use XLite\Core\Mail\Order\AAdmin;

class SubscriptionStatusAdmin extends AAdmin
{
    /**
     * @var Subscription $subscription
     */
    protected static $subscription;

    /**
     * @return array
     */
    protected static function defineVariables()
    {
        return [
                'subscriptionName' => 'My nice product',
                'subscriptionId'   => '67',
                'setupFee'         => AView::formatPrice(12),
                'subscriptionFee'  => AView::formatPrice(15),
                'planDescription'  => 'Every Monday',
                'plannedDate'      => Converter::formatDate(Converter::time()),
            ] + parent::defineVariables();
    }

    /**
     * SubscriptionStatusAdmin constructor.
     *
     * @param Order $order
     * @param Subscription $subscription
     */
    public function __construct(Order $order, Subscription $subscription)
    {
        parent::__construct($order);

        self::$subscription = $subscription;

        /** @var Plan $subscriptionPlan */
        $subscriptionPlan = $subscription->getProduct()->getXpaymentsSubscriptionPlan();

        $this->populateVariables([
            'subscriptionName' => $subscription->getProduct()->getName(),
            'subscriptionId'   => $subscription->getId(),
            'setupFee'         => AView::formatPrice($subscription->getInitialOrderItem()->getXpaymentsSetupFee()),
            'subscriptionFee'  => AView::formatPrice($subscription->getInitialOrderItem()->getXpaymentsDisplayFeePrice()),
            'planDescription'  => $subscriptionPlan->getXpaymentsPlanDescription(),
            'plannedDate'      => Converter::formatDate($subscription->getPlannedDate()),
        ]);
    }

    /**
     * Get directory
     *
     * @return string
     */
    public static function getDir()
    {
        return 'modules/XPay/XPaymentsCloud/subscription_status_' . self::$subscription->getStatus();
    }

}
