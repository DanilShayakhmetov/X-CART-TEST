<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Core;

use XLite\Module\XPay\XPaymentsCloud\Main as XPaymentsCloud;

/**
 * Apple Pay helper class
 */
class ApplePay extends \XLite\Base\Singleton
{
    /**
     * X-Payments Buy With Apple Pay cart
     *
     * @var \XLite\Model\Payment\Method
     */
    protected static $buyWithApplePayCart = null;

    /**
     * Returns true if Checkout With Apple Pay is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isCheckoutWithApplePayEnabled($order = null)
    {
        static $result;

        $index = (null !== $order) ? 1 : 0;

        if (!isset($result[$index])) {
            if (self::isBrowserMaySupportApplePay()) {
                $paymentMethod = XPaymentsCloud::getApplePayMethod();
                $result[$index] = $paymentMethod && $paymentMethod->isEnabled();

                if ($order && $result[$index]) {
                    $result[$index] = $paymentMethod->getProcessor()->isApplicable($order, $paymentMethod);
                }
            } else {
                $result[$index] = false;
            }
        }

        return $result[$index];
    }

    /**
     * Checks cart and return it only if it is not empty
     *
     * @param \XLite\Model\Cart $cart
     *
     * @return \XLite\Model\Cart|null
     */
    public static function getNotEmptyCart(\XLite\Model\Cart $cart)
    {
        if (
            $cart
            && $cart::ORDER_ZERO < $cart->getTotal()
            && $cart->checkCart()
        ) {
            $result = $cart;
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * Returns parsed list of shipping methods in specified cart
     *
     * @param \XLite\Model\Order $cart Cart
     *
     * @return array
     */
    public static function getApplePayShippingMethodsList(\XLite\Model\Order $cart)
    {
        $modifier = $cart->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');

        $list = [];
        foreach ($modifier->getRates() as $rate) {
            /** @var \XLite\Model\Shipping\Rate $rate */
            $method = $rate->getMethod();
            $appleRate = new \StdClass;
            $appleRate->label = $method->getName();
            $appleRate->detail = $method->getProcessor() ? $rate->getPreparedDeliveryTime() : $rate->getDeliveryTime();
            $appleRate->amount = round($rate->getTotalRate(),2);
            $appleRate->identifier = $method->getMethodId();
            $list[] = $appleRate;
        }

        return $list;
    }

    /**
     * Returns list of required address fields for Apple Pay
     *
     * @param string $type Either "billing" or "shipping"
     * @param \XLite\Model\Order $cart Cart
     *
     * @return array
     */
    public static function getApplePayRequiredAddressFields($type, \XLite\Model\Order $cart)
    {
        $result = [];
        if ('shipping' == $type) {
            $list = \XLite\Core\Database::getRepo('XLite\Model\AddressField')->getShippingRequiredFields();
        } else {
            $list = \XLite\Core\Database::getRepo('XLite\Model\AddressField')->getBillingRequiredFields();
        }
        foreach ($list as $field) {
            switch ($field) {
                case 'city':
                case 'country_code':
                case 'state_id':
                case 'street':
                case 'zipcode':
                    if (!in_array('postalAddress', $result)) {
                        $result[] = 'postalAddress';
                    }
                    break;
                case 'phone':
                    $result[] = 'phone';
                    break;
                case 'firstname':
                case 'lastname':
                    if (!in_array('name', $result)) {
                        $result[] = 'name';
                    }
                    break;
            }
        }

        if (
            !$cart->getProfile()
            || $cart->getProfile()->getAnonymous()
        ) {
            $result[] = 'email';
        }

        return $result;
    }

    /**
     * Check by user agent if browser can support Apple Pay at all
     *
     * @return bool
     */
    public static function isBrowserMaySupportApplePay()
    {
        $ua = \XLite\Core\Request::getInstance()->getClientUserAgent();

        return (
            false !== strpos($ua, 'Safari')
            && false === strpos($ua, 'Chrome')
            && (
                false !== strpos($ua, 'iPhone')
                || false !== strpos($ua, 'iPad')
                || false !== strpos($ua, 'Macintosh')
            )
        );
    }

    /**
     * Moves order item to Buy With Apple Pay Cart
     *
     * @param bool $doCalculate
     *
     */
    public static function getBuyWithApplePayCart($doCalculate = true)
    {
        if (is_null(static::$buyWithApplePayCart)) {
            $orderId = \XLite\Core\Session::getInstance()->buy_with_apple_pay_order_id;
            if ($orderId) {
                $cart = \XLite\Core\Database::getRepo('XLite\Model\Cart')->findOneForCustomer($orderId);
                if (
                    $cart
                    && (!$cart->hasCartStatus() || !$cart->isBuyWithApplePay())
                ) {
                    unset(\XLite\Core\Session::getInstance()->buy_with_apple_pay_order_id, $cart);
                }
            }

            if (!isset($cart)) {
                // Cart not found - create a new instance
                $cart = new \XLite\Model\Cart;
                $cart->markAsBuyWithApplePay();
                $cart->initializeCart();
            }

            static::$buyWithApplePayCart = $cart;

            if ($doCalculate) {
                $auth = \XLite\Core\Auth::getInstance();
                if ($auth->isLogged()
                    && (
                        !$cart->getProfile()
                        || $auth->getProfile()->getProfileId() != $cart->getProfile()->getProfileId()
                    )
                ) {
                    $cart->setProfile($auth->getProfile());
                    $cart->setOrigProfile($auth->getProfile());
                }

                if ($cart->isPersistent()) {
                    $cart->renew();
                    \XLite\Core\Session::getInstance()->buy_with_apple_pay_order_id = $cart->getOrderId();
                }
            }
        }

        return static::$buyWithApplePayCart;
    }

    /**
     * Add virtual Apple Pay payment method
     *
     * @return \XLite\Model\Payment\Method
     * @throws \Doctrine\ORM\ORMException
     */
    public static function addApplePayMethod()
    {
        \XLite\Core\Database::getEM()->flush();
        $pmApplePay = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->findOneBy(['service_name' => XPaymentsCloud::APPLE_PAY_SERVICE_NAME]);

        if (!$pmApplePay) {
            $pmApplePay = new \XLite\Model\Payment\Method;
            $pmApplePay->setServiceName(XPaymentsCloud::APPLE_PAY_SERVICE_NAME);
            \XLite\Core\Database::getEM()->persist($pmApplePay);

            $pmApplePay->setFromMarketplace(false);
            $pmApplePay->setAdded(true);
            $pmApplePay->setEnabled(true);
            $pmApplePay->setClass('Module\XPay\XPaymentsCloud\Model\Payment\Processor\ApplePay');
            $pmApplePay->setName('Apple Pay');
            $pmApplePay->setTitle('Apple Pay');
            $pmApplePay->setAltAdminDescription('Enable Apple Pay in your checkout via X-Payments Cloud');
            $pmApplePay->setType(\XLite\Model\Payment\Method::TYPE_CC_GATEWAY);
            \XLite\Core\Database::getEM()->flush();
        }

        return $pmApplePay;
    }

}
