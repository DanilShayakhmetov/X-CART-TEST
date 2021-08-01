<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Order;

use XLite\Core\Mailer;
use XLite\Model\Order;

abstract class ACustomer extends \XLite\Core\Mail\Order\AOrder
{
    static function getInterface()
    {
        return \XLite::CUSTOMER_INTERFACE;
    }

    public function __construct(Order $order)
    {
        parent::__construct($order);
        $this->setFrom(Mailer::getOrdersDepartmentMail());
        $this->setReplyTo(Mailer::getOrdersDepartmentMails());
        $this->setTo(['email' => $order->getProfile()->getEmail(), 'name' => $order->getProfile()->getName(false)]);
        $this->tryToSetLanguageCode($order->getProfile()->getLanguage());
    }
}