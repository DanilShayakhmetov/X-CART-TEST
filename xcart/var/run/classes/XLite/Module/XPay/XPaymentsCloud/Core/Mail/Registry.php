<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Core\Mail;

use XLite\Core\Mailer;

 class Registry extends \XLite\Core\Mail\RegistryAbstract implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected static function getNotificationsList()
    {
        return array_merge_recursive(parent::getNotificationsList(), [
            \XLite::CUSTOMER_INTERFACE =>
                [
                    Mailer::XPAYMENTS_SUBSCRIPTION_ORDER_CREATED       => OrderSubscriptionCreatedCustomer::class,
                    Mailer::XPAYMENTS_SUBSCRIPTION_SUBSCRIPTION_FAILED => SubscriptionFailedCustomer::class,
                    Mailer::XPAYMENTS_SUBSCRIPTION_PAYMENT_FAILED      => SubscriptionPaymentFailedCustomer::class,
                    Mailer::XPAYMENTS_SUBSCRIPTION_PAYMENT_SUCCESSFUL  => SubscriptionPaymentSuccessfulCustomer::class,
                    Mailer::XPAYMENTS_SUBSCRIPTION_STATUS_ACTIVE       => SubscriptionStatusCustomer::class,
                    Mailer::XPAYMENTS_SUBSCRIPTION_STATUS_STOPPED      => SubscriptionStatusCustomer::class,
                ],
            \XLite::ADMIN_INTERFACE    =>
                [
                    Mailer::XPAYMENTS_SUBSCRIPTION_ORDER_CREATED       => OrderSubscriptionCreatedAdmin::class,
                    Mailer::XPAYMENTS_SUBSCRIPTION_SUBSCRIPTION_FAILED => SubscriptionFailedAdmin::class,
                    Mailer::XPAYMENTS_SUBSCRIPTION_PAYMENT_FAILED      => SubscriptionPaymentFailedAdmin::class,
                    Mailer::XPAYMENTS_SUBSCRIPTION_PAYMENT_SUCCESSFUL  => SubscriptionPaymentSuccessfulAdmin::class,
                    Mailer::XPAYMENTS_SUBSCRIPTION_STATUS_ACTIVE       => SubscriptionStatusAdmin::class,
                    Mailer::XPAYMENTS_SUBSCRIPTION_STATUS_STOPPED      => SubscriptionStatusAdmin::class,
                ],
        ]);
    }

}
