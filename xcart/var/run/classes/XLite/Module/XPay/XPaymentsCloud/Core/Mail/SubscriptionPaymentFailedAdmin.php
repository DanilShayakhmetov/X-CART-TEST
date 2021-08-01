<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Core\Mail;

use XLite\Model\Order;
use XLite\Core\Converter;
use XLite\Core\Mailer;
use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription;

class SubscriptionPaymentFailedAdmin extends \XLite\Core\Mail\Order\AAdmin
{
    /**
     * @return array
     */
    protected static function defineVariables()
    {
        return [
                'companyName' => \XLite\Core\Config::getInstance()->Company->company_name,
                'orderNumber' => '34',
                'actualDate'    => Converter::formatDate(Converter::time()),
            ] + parent::defineVariables();
    }

    /**
     * SubscriptionPaymentFailedAdmin constructor.
     *
     * @param Order        $order
     * @param Subscription $subscription
     */
    public function __construct(Order $order, Subscription $subscription)
    {
        parent::__construct($order);

        $this->populateVariables([
            'companyName' => \XLite\Core\Config::getInstance()->Company->company_name,
            'orderNumber' => $order->getOrderNumber(),
            'actualDate'    => \XLite\Core\Converter::formatDate($subscription->getActualDate()),
        ]);
    }

    /**
     * Get directory
     *
     * @return string
     */
    public static function getDir()
    {
        return Mailer::XPAYMENTS_SUBSCRIPTION_PAYMENT_FAILED;
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
                    static::t('Payment for subscription has failed')
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
