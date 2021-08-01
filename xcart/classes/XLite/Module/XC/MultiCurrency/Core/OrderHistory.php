<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Core;


use XLite\View\AView;

class OrderHistory extends \XLite\Core\OrderHistory implements \XLite\Base\IDecorator
{
    const CODE_SELECTED_RATE = 'PLACE ORDER';

    /**
     * Register "Place order" event to the order history
     *
     * @param integer $orderId Order id
     *
     * @return void
     */
    public function registerPlaceOrder($orderId)
    {
        parent::registerPlaceOrder($orderId);

        /** @var \XLite\Module\XC\MultiCurrency\Model\Order $order */
        $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderId);

        if ($order->isMultiCurrencyOrder()) {
            $this->registerEvent(
                $orderId,
                static::CODE_SELECTED_RATE,
                'Note: Invoice in {{currency}}. Order billed in {{real_currency}}. Exchange rate is {{exchange_rate}}.',
                [
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
        }
    }

    /**
     * Get default currency text
     *
     * @param \XLite\Model\Currency $defaultCurrency Currency
     *
     * @return string
     */
    protected function getDefaultCurrencyText(\XLite\Model\Currency $defaultCurrency)
    {
        $prefix = $defaultCurrency->getPrefix();

        $prefix = empty($prefix) ? $defaultCurrency->getSuffix() : $prefix;
        $prefix = empty($prefix) ? '' : ' (' . $prefix . ')';

        return $defaultCurrency->getCode() . $prefix;
    }

    /**
     * Get selected currency text
     *
     * @param \XLite\Model\Currency $selectedCurrency Selected currency
     * @param float                 $rate             Rate
     * @param \XLite\Model\Currency $defaultCurrency  Default currency
     *
     * @return string
     */
    protected function getSelectedCurrencyRateText(\XLite\Model\Currency $selectedCurrency, $rate, \XLite\Model\Currency $defaultCurrency)
    {
        $rate = 1 / $rate;

        $precision = $defaultCurrency->getE();

        $defaultCurrency->setE(4);

        $return = AView::formatPrice(1, $selectedCurrency, false, true)
            . ' = ' . AView::formatPrice($rate, $defaultCurrency, false, true);

        $defaultCurrency->setE($precision);

        return $return;
    }
}
