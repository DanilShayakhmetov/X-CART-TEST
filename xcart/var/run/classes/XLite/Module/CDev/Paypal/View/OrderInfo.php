<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View;
use XLite\Model\Payment\BackendTransaction;

/**
 * Extend Order details page widget
 */
 class OrderInfo extends \XLite\Module\QSL\BraintreeVZ\View\Order\Details\Admin\Info implements \XLite\Base\IDecorator
{
    /**
     * getCSSFiles 
     * 
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Paypal/order/style.css';

        return $list;
    }

    /**
     * @return bool
     */
    public function isRefundInProgress()
    {
        $result = false;

        /** @var \XLite\Model\Order $order */
        $order = $this->getOrder();
        $transactions = $order
            ? $order->getPaymentTransactions()
            : [];

        foreach ($transactions as $t) {
            $backendTransactions = $t->getBackendTransactions();

            if (!$backendTransactions) {
                continue;
            }

            foreach ($backendTransactions as $bt) {
                /** @var BackendTransaction $bt */
                if ($bt->isRefund()
                    && in_array($bt->getStatus(), [ $bt::STATUS_INPROGRESS, $bt::STATUS_PENDING ], true)
                ) {
                    $result = true;
                    break 2;
                }
            }
        }

        return $result;
    }
}
