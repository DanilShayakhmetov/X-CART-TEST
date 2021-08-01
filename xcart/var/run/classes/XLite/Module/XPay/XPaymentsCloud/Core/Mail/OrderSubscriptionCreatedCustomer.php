<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Core\Mail;

use XLite\Model\Order;
use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription;
use XLite\Core\Converter;
use XLite\Core\Mailer;

class OrderSubscriptionCreatedCustomer extends \XLite\Core\Mail\Order\ACustomer
{
    /**
     * @return array
     */
    protected static function defineVariables()
    {
        return [
                'subscriptionId'       => '67',
                'orderNumber'          => '42',
                'pendingPaymentNumber' => '7 payment total of 12',
                'actualDate'             => Converter::formatDate(Converter::time()),
            ] + parent::defineVariables();
    }

    /**
     * OrderSubscriptionCreatedCustomer constructor.
     *
     * @param Order        $order
     * @param Subscription $subscription
     */
    public function __construct(Order $order, Subscription $subscription)
    {
        parent::__construct($order);

        $this->populateVariables([
            'subscriptionId'       => $subscription->getId(),
            'orderNumber'          => $order->getOrderNumber(),
            'pendingPaymentNumber' => $subscription->getPeriods()
                ? '<br>' . static::t('X payment total of X', [
                    'payment'  => $subscription->getPendingPaymentNumber(),
                    'payments' => $subscription->getPeriods()
                ])
                : '',
            'actualDate'             => Converter::formatDate($subscription->getActualDate()),
        ]);
    }

    /**
     * Get directory
     *
     * @return string
     */
    public static function getDir()
    {
        return Mailer::XPAYMENTS_SUBSCRIPTION_ORDER_CREATED;
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
                    static::t('Order for subscription is initially created')
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
