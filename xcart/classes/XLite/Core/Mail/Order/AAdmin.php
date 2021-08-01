<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Order;

use XLite\Core\Converter;
use XLite\Core\Mailer;
use XLite\Model\Order;

abstract class AAdmin extends \XLite\Core\Mail\Order\AOrder
{
    static function getInterface()
    {
        return \XLite::ADMIN_INTERFACE;
    }

    protected static function defineVariables()
    {
        return [
                'customer_name' => '',
                'first_name'    => static::t('na_admin'),
                'order_link'    => \XLite::getInstance()->getShopURL(),
            ] + parent::defineVariables();
    }

    public function __construct(Order $order)
    {
        parent::__construct($order);
        $this->setFrom(Mailer::getOrdersDepartmentMail());
        $this->addReplyTo([
            'address' => $order->getProfile()->getEmail(),
            'name'    => $order->getProfile()->getName(false),
        ]);
        $this->setTo(Mailer::getOrdersDepartmentMails());

        $this->populateVariables([
            'customer_name' => $order->getProfile()->getName(),
            'first_name'    => static::t('na_admin'),
            'order_link'    => Converter::buildFullURL(
                'order',
                '',
                ['order_number' => $order->getOrderNumber()],
                \XLite::getAdminScript()
            ),
        ]);
    }
}