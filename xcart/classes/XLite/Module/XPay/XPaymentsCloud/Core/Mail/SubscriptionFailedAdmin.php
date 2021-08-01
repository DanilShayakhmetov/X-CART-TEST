<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Core\Mail;

use XLite\Model\Order;
use XLite\Core\Mailer;
use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription;

class SubscriptionFailedAdmin extends \XLite\Core\Mail\Order\AAdmin
{
    /**
     * @return array
     */
    protected static function defineVariables()
    {
        return [
                'subscriptionId'  => '67',
                'reason'          => 'Shipping method is unavailable',
            ] + parent::defineVariables();
    }

    /**
     * SubscriptionFailedAdmin constructor.
     *
     * @param Order        $order Order model
     * @param Subscription $subscription
     * @param string       $reason Reason for failing subscription
     */
    public function __construct(Order $order, Subscription $subscription, string $reason = '')
    {
        parent::__construct($order);

        $this->populateVariables([
            'subscriptionId' => $subscription->getId(),
            'reason'         => $reason
                ? '<p>' . static::t('Reason') . ': ' . $reason . '</p>'
                : '',
        ]);
    }

    /**
     * Get directory
     *
     * @return string
     */
    public static function getDir()
    {
        return Mailer::XPAYMENTS_SUBSCRIPTION_SUBSCRIPTION_FAILED;
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
                    static::t('Subscription has failed')
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