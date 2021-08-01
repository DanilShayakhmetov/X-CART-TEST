<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Button;

use XLite\Module\XPay\XPaymentsCloud\Main as XPaymentsCloud;
use \XLite\Module\XPay\XPaymentsCloud\Core\ApplePay as XPaymentsApplePay;

/**
 * Checkout with Apple Pay base button
 */
abstract class ACheckoutWithApplePay extends \XLite\View\Button\Regular
{
    /**
     * @return boolean
     */
    protected function isVisible()
    {
        $cart = $this->getNotEmptyCart();

        return
            parent::isVisible()
            && XPaymentsApplePay::isCheckoutWithApplePayEnabled($cart);
    }

    /**
     * Checks current cart and return it only if it is not empty
     *
     * @return \XLite\Model\Cart
     */
    protected function getNotEmptyCart()
    {
        return XPaymentsApplePay::getNotEmptyCart($this->getCart());
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XPay/XPaymentsCloud/button/checkout_apple_pay.twig';
    }

    /**
     * Return list of required JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XPay/XPaymentsCloud/checkout/widget.js';
        $list[] = 'modules/XPay/XPaymentsCloud/button/checkout_apple_pay.js';

        return $list;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XPay/XPaymentsCloud/button/checkout_apple_pay.css';

        return $list;
    }

    /**
     * Return X-Payments Cloud payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    public function getPaymentMethod()
    {
        return XPaymentsCloud::getPaymentMethod();
    }

    /**
     * Returns CSS class
     *
     * @return string
     */
    protected function getButtonClass()
    {
        return 'apple-pay-button';
    }

    /**
     * Returns html tag for button container
     *
     * @return string
     */
    protected function getContainerTag()
    {
        return 'div';
    }

    /**
     * Returns button label for old devices
     *
     * @return string
     */
    protected function getButtonLabel()
    {
        return 'Check out with';
    }

    /**
     * It is used to indicate it is Buy or Checkout button
     *
     * @return string
     */
    protected function getButtonMode()
    {
        return 'checkout';
    }

    /**
     * Checks if X-Payments Cloud method is available in checkout
     *
     * @return bool
     */
    public function isXpaymentsMethodAvailableCheckout()
    {
        static $result = null;

        if (is_null($result)) {
            $result = false;
            if ($this->getCart()) {
                foreach ($this->getCart()->getPaymentMethods() as $method) {
                    if (XPaymentsCloud::XPAYMENTS_SERVICE_NAME == $method->getServiceName()) {
                        $result = true;
                        break;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Returns JSON-encoded required address fields list for Apple Pay
     *
     * @param string $type Either "shipping" or "billing"
     *
     * @return string
     */
    protected function getRequiredAddressFields($type = 'shipping')
    {
        $result = XPaymentsApplePay::getApplePayRequiredAddressFields($type, $this->getCart());
        return json_encode($result);
    }

    /**
     * Returns JSON-encoded shipping methods list for Apple Pay
     *
     * @return string
     */
    protected function getShippingMethodsList()
    {
        $result = XPaymentsApplePay::getApplePayShippingMethodsList($this->getCart());
        return json_encode($result);
    }
 }
