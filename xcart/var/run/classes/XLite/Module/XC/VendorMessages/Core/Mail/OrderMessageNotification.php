<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Core\Mail;


use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Model\AccessControlZoneType;
use XLite\Model\Order;
use XLite\Model\Profile;
use XLite\Module\XC\VendorMessages\Model\Message;

class OrderMessageNotification extends AMessageNotification
{
    static function getDir()
    {
        return 'modules/XC/VendorMessages/notification';
    }

    protected static function defineVariables()
    {
        return array_merge(parent::defineVariables(), [
            'order_number'        => '42',
            'order_link'          => \XLite::getInstance()->getShopURL(),
            'order_messages_link' => sprintf('<a href="%s">%s</a>', \XLite::getInstance()->getShopURL(), '42'),
        ]);
    }

    public function __construct(Message $message, Profile $recipient = null)
    {
        parent::__construct($message, $recipient);

        $order = $message->getConversation()->getOrder();

        $this->populateVariables([
            'order_number'        => $order->getOrderNumber(),
            'order_link'          => \XLite::getInstance()->getShopURL(Converter::buildURL(
                'order',
                null,
                ['order_number' => $order->getOrderNumber()],
                $recipient && !$recipient->isAdmin()
                    ? \XLite::getCustomerScript()
                    : \XLite::getAdminScript()
            )),
            'order_messages_link' => $this->getOrderMessagesLink($order, $recipient),
        ]);

        $this->appendData([
            'recipient_name' => $recipient
                ? $recipient->getNameForMessages()
                : Config::getInstance()->Company->company_name,
        ]);
    }

    /**
     * @param Order   $order
     * @param Profile $recipient
     *
     * @return string
     * @throws \Exception
     */
    protected function getOrderMessagesLink(Order $order, Profile $recipient = null)
    {
        if ($recipient && !$recipient->isAdmin()) {
            if ($order->getProfile()->getAnonymous()) {
                $acc = \XLite\Core\Database::getRepo('XLite\Model\AccessControlCell')->generateAccessControlCell(
                    [$order],
                    [AccessControlZoneType::ZONE_TYPE_ORDER],
                    'resendAccessLink'
                );

                $url = Converter::buildPersistentAccessURL(
                    $acc,
                    'order_messages',
                    '',
                    ['order_number' => $order->getOrderNumber()],
                    \XLite::getCustomerScript()
                );
            } else {
                $url = Converter::buildURL(
                    'order_messages',
                    null,
                    ['order_number' => $order->getOrderNumber()],
                    \XLite::getCustomerScript()
                );
            }
        } else {
            $url = Converter::buildURL(
                'order',
                null,
                [
                    'page'         => 'messages',
                    'order_number' => $order->getOrderNumber(),
                ],
                \XLite::getAdminScript()
            );
        }

        return sprintf(
            '<a href="%s">%s</a>',
            htmlentities(\XLite::getInstance()->getShopURL($url)),
            $order->getOrderNumber()
        );
    }
}
