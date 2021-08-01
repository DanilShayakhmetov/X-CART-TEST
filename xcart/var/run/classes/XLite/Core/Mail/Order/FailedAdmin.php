<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Order;


class FailedAdmin extends \XLite\Core\Mail\Order\AAdmin
{
    static function getDir()
    {
        return 'order_failed';
    }

    public function send()
    {
        $result = parent::send();

        if ($order = $this->getOrder()) {
            if ($result) {
                \XLite\Core\OrderHistory::getInstance()->registerAdminEmailSent(
                    $order->getOrderId(),
                    'Order is failed'
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