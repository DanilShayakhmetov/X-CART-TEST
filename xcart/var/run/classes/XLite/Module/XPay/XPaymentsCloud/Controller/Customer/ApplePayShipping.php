<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Controller\Customer;

use \XLite\Module\XPay\XPaymentsCloud\Main as XPaymentsCloud;
use \XLite\Module\XPay\XPaymentsCloud\Core\ApplePay as XPaymentsApplePay;
/**
 * Shipping estimator
 */
class ApplePayShipping extends \XLite\Controller\Customer\ShippingEstimate
{

    /**
     * Compose object with cart totals
     *
     * @return \StdClass
     */
    protected function composeTotals()
    {
        $cart = $this->getCart();

        $result = array(
            'newTotal' => array(
                'amount' => $cart->getTotal(),
                'type'   => 'final',
            ),
            'newLineItems' => array(
                array(
                    'label'  => static::t('Subtotal'),
                    'amount' => $cart->getDisplaySubtotal(),
                    'type'   => 'final',
                ),
            )
        );

        $tax = $cart->getSurchargesSubtotal(\XLite\Model\Base\Surcharge::TYPE_TAX, false);
        if ($cart::ORDER_ZERO < $tax) {
            $result['newLineItems'][] = array(
                'label'  => static::t('Tax'),
                'amount' => $tax,
                'type'   => 'final',
            );
        }

        $discount = $cart->getSurchargesSubtotal(\XLite\Model\Base\Surcharge::TYPE_DISCOUNT, false);
        if ($cart::ORDER_ZERO < abs($discount)) {
            $result['newLineItems'][] = array(
                'label'  => static::t('Discount'),
                'amount' => $discount,
                'type'   => 'final',
            );
        }

        return (object) $result;
    }

    /**
     * Set estimate destination
     *
     * @return void
     */
    protected function doActionSetDestination()
    {
        $this->getCart()->setPaymentMethod(XPaymentsCloud::getApplePayMethod());

        parent::doActionSetDestination();

        $this->setPureAction(true);
        $this->setInternalRedirect(false);

        $result = $this->composeTotals();

        if ($this->valid) {
            $result->newShippingMethods = XPaymentsApplePay::getApplePayShippingMethodsList($this->getCart());
        } else {
            $error = new \StdClass();
            $error->code = 'shippingContactInvalid';
            $error->contactField = 'postalAddress';
            $error->message = static::t('Shipping address is invalid');

            $result->errors = [
                $error
            ];
        }

        echo json_encode($result);
    }

    /**
     * Change shipping method
     *
     * @return void
     */
    protected function doActionChangeMethod()
    {
        $this->getCart()->setPaymentMethod(XPaymentsCloud::getApplePayMethod());

        parent::doActionChangeMethod();

        $this->setPureAction(true);
        $this->setInternalRedirect(false);

        $this->updateCart();

        $result = $this->composeTotals();

        echo json_encode($result);
    }

    /**
     * Force silent flag to avoid reloads when Apple Pay shipping selected
     *
     * @param boolean $silent
     *
     * @throws \Exception
     */
    protected function updateCart($silent = false)
    {
        parent::updateCart(true);
    }

    /**
     * Return cart instance or Buy With Apple Pay Cart
     *
     * @param null|boolean $doCalculate Flag: completely recalculate cart if true OPTIONAL
     *
     * @return \XLite\Model\Order
     */
    public function getCart($doCalculate = null)
    {
        if (\XLite\Core\Request::getInstance()->xpaymentsBuyWithApplePay) {
            $cart = XPaymentsApplePay::getBuyWithApplePayCart(null !== $doCalculate ? $doCalculate : $this->markCartCalculate());
        } else {
            $cart = parent::getCart($doCalculate);
        }

        return $cart;
    }

}
