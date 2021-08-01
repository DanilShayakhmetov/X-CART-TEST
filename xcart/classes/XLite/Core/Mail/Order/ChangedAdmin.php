<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Order;


class ChangedAdmin extends \XLite\Core\Mail\Order\AAdmin
{
    static function getDir()
    {
        return 'order_changed';
    }

    public function handleSendSuccess()
    {
        parent::handleSendSuccess();

        \XLite\Core\OrderHistory::getInstance()->registerAdminEmailSent(
            $this->getOrder()->getOrderId(),
            'Order is changed'
        );
    }

    public function handleSendError($error, $message)
    {
        parent::handleSendError($error, $message);

        \XLite\Core\OrderHistory::getInstance()->registerAdminEmailFailed(
            $this->getOrder()->getOrderId(),
            $message
        );
    }
}