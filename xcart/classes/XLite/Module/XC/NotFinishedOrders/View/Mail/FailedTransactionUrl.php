<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\View\Mail;

use XLite\Model\Payment\Transaction;


/**
 * FailedTransactionUrl
 *
 * @ListChild(list="failed_transaction.after", zone="mail")
 */
class FailedTransactionUrl extends \XLite\View\AView
{
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getTransaction() instanceof Transaction;
    }

    protected function getDefaultTemplate()
    {
        return 'modules/XC/NotFinishedOrders/failed_transaction/body.twig';
    }

    /**
     * @return Transaction|null
     */
    protected function getTransaction()
    {
        return isset($this->transaction) ? $this->transaction : null;
    }

    /**
     * @return boolean
     */
    protected function isNfo()
    {
        $order = $this->getTransaction()->getOrder();

        return $order
            && is_null($order->getShippingStatus());
    }

    /**
     * @return string
     */
    protected function getNfoUrl()
    {
        return \XLite\Core\Converter::buildFullURL('order', '', [
            'order_id' => $this->getTransaction()->getOrder()->getOrderId(),
        ], \XLite::getAdminScript());
    }
}