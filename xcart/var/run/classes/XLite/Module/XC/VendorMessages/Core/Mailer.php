<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Core;

use XLite\Module\XC\VendorMessages\Core\Mail\NewMessageNotification;
use XLite\Module\XC\VendorMessages\Core\Mail\OrderMessageNotification;

/**
 * Mailer
 */
abstract class Mailer extends \XLite\Module\XPay\XPaymentsCloud\Core\Mailer implements \XLite\Base\IDecorator
{
    const RECIPIENT_ADMIN    = 'A';
    const RECIPIENT_CUSTOMER = 'C';

    /**
     * @param \XLite\Module\XC\VendorMessages\Model\Message $message Message
     */
    public static function sendMessageNotifications(\XLite\Module\XC\VendorMessages\Model\Message $message)
    {
        foreach ($message->getConversation()->getMembers() as $member) {
            if ($message->getAuthor()->getProfileId() !== $member->getProfileId()) {
                (new NewMessageNotification($message, $member))->schedule();
            }
        }

        if ($message->isShouldSendToAdmin()) {
            (new NewMessageNotification($message))->schedule();
        }
    }

    /**
     * @param \XLite\Module\XC\VendorMessages\Model\Message $message Message
     */
    public static function sendOrderMessageNotifications(\XLite\Module\XC\VendorMessages\Model\Message $message)
    {
        foreach ($message->getConversation()->getMembers() as $member) {
            if ($message->getAuthor()->getProfileId() !== $member->getProfileId()) {
                (new OrderMessageNotification($message, $member))->schedule();
            }
        }

        if ($message->isShouldSendToAdmin()) {
            (new OrderMessageNotification($message))->schedule();
        }
    }
}