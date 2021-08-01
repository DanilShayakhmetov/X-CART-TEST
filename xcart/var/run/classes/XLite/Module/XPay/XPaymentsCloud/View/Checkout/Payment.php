<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Checkout;

use XLite\Module\XPay\XPaymentsCloud\Model\Payment\Processor\XPaymentsCloud;

/**
 * Payment template
 */
abstract class Payment extends \XLite\View\Checkout\PaymentAbstract implements \XLite\Base\IDecorator
{
    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        if ($this->isXpaymentsMethodAvailable()) {
            $list[] = 'modules/XPay/XPaymentsCloud/checkout/widget.css';
        }
        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        if ($this->isXpaymentsMethodAvailable()) {
            $list[] = 'modules/XPay/XPaymentsCloud/checkout/widget.js';
            $list[] = 'modules/XPay/XPaymentsCloud/checkout/apple_pay_method.js';
        }
        return $list;
    }

    /**
     * Checks if X-Payments Cloud method is available in checkout
     *
     * @return bool
     */
    public function isXpaymentsMethodAvailable()
    {
        static $result = null;

        if (is_null($result)) {
            $result = false;
            foreach ($this->getCart()->getPaymentMethods() as $method) {
                if ('Module\XPay\XPaymentsCloud\Model\Payment\Processor\XPaymentsCloud' == $method->getClass()) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

}
