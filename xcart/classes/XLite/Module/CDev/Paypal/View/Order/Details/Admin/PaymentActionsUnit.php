<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Order\Details\Admin;

class PaymentActionsUnit extends \XLite\View\Order\Details\Admin\PaymentActionsUnit implements \XLite\Base\IDecorator
{
    /**
     * @return string
     */
    protected function getTemplate()
    {
        /** @var \XLite\Model\Payment\Transaction $transaction */
        $transaction       = $this->getParam(self::PARAM_TRANSACTION);
        $methodServiceName = $transaction->getPaymentMethod()->getServiceName();

        $unit = $this->getParam(self::PARAM_UNIT);

        if ($methodServiceName === \XLite\Module\CDev\Paypal\Main::PP_METHOD_PFM
            && $unit === \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_PAYOUT) {

            return 'modules/CDev/Paypal/order/payment_actions/payout.twig';
        }

        return parent::getTemplate();
    }

    protected function getPayouts()
    {
        $result = [];

        /** @var \XLite\Model\Payment\Transaction $transaction */
        $transaction = $this->getParam(self::PARAM_TRANSACTION);

        /** @var \XLite\Model\Payment\BackendTransaction[] $backendTransactions */
        $backendTransactions = $transaction->getBackendTransactions();
        foreach ($backendTransactions as $backendTransaction) {
            if ($backendTransaction->getType() !== \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE) {
                continue;
            }

            $captureId = $backendTransaction->getDataCell('capture_id');
            $payee     = $backendTransaction->getDataCell('payee');
            $payoutId  = $backendTransaction->getDataCell('payout_transaction_id');
            $vendorId  = $backendTransaction->getDataCell('vendor_id');
            $payout    = $backendTransaction->getDataCell('payout_amount');
            $currency  = $backendTransaction->getDataCell('payout_currency');

            if ($captureId && $payee && !$payoutId) {
                $result[] = [
                    'captureId'              => $captureId->getValue(),
                    'payee'                  => $payee->getValue(),
                    'transactionReferenceId' => $backendTransaction->getDataCell('transaction_reference_id')->getValue(),
                    'vendorId'               => $vendorId ? $vendorId->getValue() : null,
                    'payout'                 => $payout ? $payout->getValue() : null,
                    'currency'               => $currency ? $currency->getValue() : null,
                ];
            }
        }

        return $result;
    }

    protected function getPayoutURL($payout)
    {
        return $this->buildURL(
            'order',
            $this->getParam(self::PARAM_UNIT),
            [
                'order_number' => $this->getParam(self::PARAM_ORDER_NUMBER),
                'trn_id'       => $this->getParam(self::PARAM_TRANSACTION)->getTransactionId(),
                'capture_id'   => $payout['captureId'],
            ]
        );

    }

    /**
     * @param $payout
     *
     * @return mixed
     */
    protected function getPayee($payout)
    {
        return $payout['payee'];
    }

    /**
     * @param array $payout
     *
     * @return null|\XLite\Model\AEntity
     */
    protected function getVendor($payout)
    {
        if (preg_match('/-t$/', $payout['transactionReferenceId'])) {

            return null;
        }

        if ($payout['vendorId']) {
            $vendor = \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($payout['vendorId']);

            if ($vendor && $vendor->isVendor()) {

                return $vendor;
            }
        }

        return null;
    }

    protected function getPayoutUnitName($payout)
    {
        $result = $this->getUnitName();

        if ($payout['payout']) {
            /** @var \XLite\Model\Currency $currency */
            $currency = \XLite\Core\Database::getRepo('XLite\Model\Currency')->findOneByCode($payout['currency']);

            if ($currency) {
                $payout = static::formatPrice($payout['payout'], $currency);
            } else {
                $payout = static::formatPrice($payout['payout']);
            }

            $result .= ' ' . $payout;
        }

        return $result;
    }
}
