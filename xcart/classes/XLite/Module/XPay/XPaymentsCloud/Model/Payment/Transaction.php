<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Model\Payment;

use XLite\Module\XPay\XPaymentsCloud\Model\Payment\XpaymentsFraudCheckData as FraudCheckData;

class Transaction extends \XLite\Model\Payment\Transaction implements \XLite\Base\IDecorator
{
    /**
     * One-to-many relation with X-Payments Cloud payment fraud check data
     *
     * @var FraudCheckData
     *
     * @OneToMany (targetEntity="\XLite\Module\XPay\XPaymentsCloud\Model\Payment\XpaymentsFraudCheckData", mappedBy="transaction", cascade={"all"})
     */
    protected $xpaymentsFraudCheckData;

    /**
     * Checks if this transaction should be processed by XPaymentsCloud module
     *
     * @return bool
     */
    public function isXpayments()
    {
        return !empty($this->getXpaymentsId());
    }

    /**
     * Set xpaymentsPaymentId data cell
     *
     * @param $xpid
     *
     * @return void
     */
    public function setXpaymentsId($xpid)
    {
        $this->setDataCell('xpaymentsPaymentId', $xpid, 'X-Payments ID', 'C');
    }

    /**
     * Get xpaymentsPaymentId data cell
     *
     * @return string
     */
    public function getXpaymentsId()
    {
        return $this->getDetail('xpaymentsPaymentId');
    }

    /**
     * Get charge value modifier
     *
     * @return float
     */
    public function getChargeValueModifier()
    {
        if (!$this->isXpayments()) {
            return parent::getChargeValueModifier();
        }

        // Allow partial capture

        $value = 0;
        $valueCaptured = 0;
        $valueRefunded = 0;

        if ($this->isCompleted() || $this->isPending()) {
            $value += $this->getValue();
        }

        if ($this->getBackendTransactions()) {
            /** @var \XLite\Model\Payment\BackendTransaction $transaction */
            foreach ($this->getBackendTransactions() as $transaction) {
                if ($transaction->isCapture() && $transaction->isSucceed()) {;
                    $valueCaptured += abs($transaction->getValue());
                }

                if ($transaction->isRefund() && $transaction->isSucceed()) {
                    $valueRefunded += abs($transaction->getValue());
                }
            }
        }

        if ($valueCaptured < 0.01) {
            $valueCaptured = $value;
        }

        return max(
            0,
            min($valueCaptured, $value) - $valueRefunded
        );
    }

    /**
     * Returns true if payment has successful ACCEPT transaction
     *
     * @return boolean
     */
    public function isAccepted()
    {
        $result = false;

        if ($this->getBackendTransactions()) {
            foreach ($this->getBackendTransactions() as $transaction) {
                if (
                    $transaction->isAccept()
                    && $transaction->isSucceed()
                ) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Returns true if payment has successful DECLINE transaction
     *
     * @return boolean
     */
    public function isDeclined()
    {
        $result = false;

        if ($this->getBackendTransactions()) {
            foreach ($this->getBackendTransactions() as $transaction) {
                if (
                    $transaction->isDecline()
                    && $transaction->isSucceed()
                ) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Add xpaymentsFraudCheckData
     *
     * @param FraudCheckData $fraudCheckData
     * @return Transaction
     */
    public function addXpaymentsFraudCheckData(FraudCheckData $fraudCheckData)
    {
        $this->xpaymentsFraudCheckData[] = $fraudCheckData;
        return $this;
    }

    /**
     * Get xpaymentsFraudCheckData
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getXpaymentsFraudCheckData()
    {
        return $this->xpaymentsFraudCheckData;
    }

}
