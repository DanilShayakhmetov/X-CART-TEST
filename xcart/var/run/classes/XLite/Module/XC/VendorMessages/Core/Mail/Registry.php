<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Core\Mail;


 class Registry extends \XLite\Module\XPay\XPaymentsCloud\Core\Mail\Registry implements \XLite\Base\IDecorator
{
    protected static function getNotificationsList()
    {
        return array_merge_recursive(parent::getNotificationsList(), [
            \XLite::CUSTOMER_INTERFACE => [
                'modules/XC/VendorMessages/new_message_notification' => NewMessageNotification::class,
                'modules/XC/VendorMessages/notification'             => OrderMessageNotification::class,
            ],
        ]);
    }
}