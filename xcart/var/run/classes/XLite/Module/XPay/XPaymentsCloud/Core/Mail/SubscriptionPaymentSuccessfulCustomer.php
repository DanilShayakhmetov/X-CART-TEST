<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Core\Mail;

use XLite\Core\Converter;
use XLite\Core\Mailer;
use XLite\Model\Order;
use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription;

class SubscriptionPaymentSuccessfulCustomer extends \XLite\Core\Mail\Order\ACustomer
{
    /**
     * @return array
     */
    protected static function defineVariables()
    {
        return [
                'subscriptionId' => '67',
                'plannedDate'    => Converter::formatDate(Converter::time()),
                'pageUrl'        => Mailer::getXpaymentsSubscriptionsPageUrlWithTags(),
            ] + parent::defineVariables();
    }

    /**
     * SubscriptionPaymentSuccessfulCustomer constructor.
     *
     * @param Order        $order
     * @param Subscription $subscription
     */
    public function __construct(Order $order, Subscription $subscription)
    {
        parent::__construct($order);

        $this->populateVariables([
            'subscriptionId' => $subscription->getId(),
            'plannedDate'    => \XLite\Core\Converter::formatDate($subscription->getPlannedDate()),
            'pageUrl'        => Mailer::getXpaymentsSubscriptionsPageUrlWithTags(),
        ]);
    }

    /**
     * Get directory
     *
     * @return string
     */
    public static function getDir()
    {
        return Mailer::XPAYMENTS_SUBSCRIPTION_PAYMENT_SUCCESSFUL;
    }

    /**
     * @return bool
     */
    public function send()
    {
        $result = parent::send();

        $order = $this->getOrder();

        if ($order) {
            if ($result) {
                \XLite\Core\OrderHistory::getInstance()->registerAdminEmailSent(
                    $order->getOrderId(),
                    static::t('Payment for subscription is successful')
                );
            } else {
                \XLite\Core\OrderHistory::getInstance()->registerAdminEmailFailed(
                    $order->getOrderId()
                );
            }
        }

        return $result;
    }

}
