<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XPay\XPaymentsCloud\View\Order\Details\Admin;

use XLite\Model\Payment\BackendTransaction;
use XLite\Model\Payment\Transaction;

/**
 * Payment actions widget extend for partial capture
 */
 class PaymentActions extends \XLite\View\Order\Details\Admin\PaymentActionsAbstract implements \XLite\Base\IDecorator
{

    /**
     * Get list of allowed backend transactions
     *
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction
     *
     * @return array
     */
    protected function getTransactionUnits($transaction = null)
    {
        parent::getTransactionUnits($transaction);

        if (isset($transaction) && $transaction->getPaymentMethod()) {
            if ($transaction->isXpayments()) {
                if (in_array(BackendTransaction::TRAN_TYPE_CAPTURE_MULTI, $this->allowedTransactions)) {
                    $this->allowedTransactions = array_diff($this->allowedTransactions, [
                        BackendTransaction::TRAN_TYPE_CAPTURE,
                        BackendTransaction::TRAN_TYPE_CAPTURE_PART,
                    ]);
                } elseif (in_array(BackendTransaction::TRAN_TYPE_CAPTURE_PART, $this->allowedTransactions)) {
                    $this->allowedTransactions = array_diff($this->allowedTransactions, [
                        BackendTransaction::TRAN_TYPE_CAPTURE,
                    ]);
                }
            }
        }

        return $this->allowedTransactions;
    }

}

