<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View;

/**
 * Class AomChargeWarning
 * @package XLite\Module\XC\MultiCurrency\View
 */
class AomChargeWarning extends \XLite\Module\XC\MultiCurrency\View\RealChargeWarning
{
    /**
     * Get note
     *
     * @return string
     */
    protected function getSelectedRateText()
    {
        $order = $this->getOrder();

        if (
            isset($order)
            && $order->isMultiCurrencyOrder()
        ) {
            $return = static::t(
                'Note: [Invoice] in {{currency}}. Order billed in {{real_currency}}. Exchange rate is {{exchange_rate}}.',
                [
                    'link' => $this->getInvoiceURL(),
                    'currency' => $this->getDefaultCurrencyText(
                        $order->getSelectedMultiCurrency()
                    ),
                    'real_currency' => $this->getDefaultCurrencyText(
                        $order->getCurrency()
                    ),
                    'exchange_rate' => $this->getSelectedCurrencyRateText(
                        $order->getSelectedMultiCurrency(),
                        $order->getSelectedMultiCurrencyRate(),
                        $order->getCurrency()
                    )
                ]
            );
        } else {
            $return = '';
        }

        return $return;
    }

    /**
     * @return string
     */
    protected function getInvoiceURL()
    {
        return $this->getOrder()
            ? $this->buildURL('order', '', $this->getInvoiceUrlParams($this->getOrder()))
            : '';
    }

    protected function getInvoiceUrlParams(\XLite\Model\Order $order)
    {
        $result = [
            'page' => 'invoice',
        ];

        if ($order->getOrderNumber()) {
            $result['order_number'] = $order->getOrderNumber();
        } else {
            $result['order_id'] = $order->getOrderId();
        }

        return $result;
    }
}
