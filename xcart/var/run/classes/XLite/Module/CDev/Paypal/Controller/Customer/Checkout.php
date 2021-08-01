<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Controller\Customer;

use \XLite\Module\CDev\Paypal;

/**
 * Checkout controller
 */
 class Checkout extends \XLite\Module\XPay\XPaymentsCloud\Controller\Customer\Checkout implements \XLite\Base\IDecorator
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(
            parent::defineFreeFormIdActions(),
            [
                'start_express_checkout',
                'express_checkout_return',
            ]
        );
    }

    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->isReturnedAfterExpressCheckout() || $this->isReturnedAfterPaypalCommercePlatform()
            ? static::t('Review & Submit order')
            : parent::getTitle();
    }

    /**
     * Check if customer is returned from PP EC
     *
     * @return boolean
     */
    public function isReturnedAfterExpressCheckout()
    {
        return \XLite\Core\Request::getInstance()->ec_returned === '1';
    }

    /**
     * Check if customer is returned from PP EC
     *
     * @return boolean
     */
    public function isReturnedAfterPaypalCommercePlatform()
    {
        $transaction = $this->getCart()->getFirstOpenPaymentTransaction();

        return $transaction && $transaction->getDataCell('PaypalOrderId') && $transaction->getDataCell('PaypalPayerId');
    }

    /**
     * @return string
     */
    public function getPaymentMethodServiceName()
    {
        return $this->getCart()->getPaymentMethod()->getServiceName();
    }

    /**
     * Order placement is success
     *
     * @param boolean $fullProcess Full process or not OPTIONAL
     */
    public function processSucceed($fullProcess = true)
    {
        parent::processSucceed($fullProcess);

        if (\XLite\Core\Request::getInstance()->inContext) {
            \XLite\Core\Session::getInstance()->inContextRedirect = true;
            \XLite\Core\Session::getInstance()->cancelUrl         = \XLite\Core\Request::getInstance()->cancelUrl;
        }
    }

    /**
     * @param $note
     */
    protected function setOrderNote($note)
    {
        $this->getCart()->setNotes($note);
    }

    /**
     * Set order note
     * @unused
     */
    public function doActionSetOrderNote()
    {
        if (isset(\XLite\Core\Request::getInstance()->notes)) {
            $this->setOrderNote(\XLite\Core\Request::getInstance()->notes);
        }
        \XLite\Core\Database::getEM()->flush();
        exit();
    }

    /**
     * Prepare profile and address for quick checkout
     */
    protected function prepareProfile()
    {
        $profile = $this->getCartProfile();

        if ($profile && !$profile->getFirstAddress()) {
            $address = \XLite\Model\Address::createDefaultShippingAddress();
            $address->setProfile($profile);
            $address->setIsBilling(true);
            $address->setIsShipping(true);
            $address->setIsWork(true);
            $profile->addAddresses($address);

            \XLite\Core\Database::getEM()->persist($address);
        }

        \XLite\Core\Database::getEM()->flush($profile);
    }

    /**
     * doActionStartExpressCheckout
     */
    protected function doActionStartExpressCheckout()
    {
        if (Paypal\Main::isExpressCheckoutEnabled() || Paypal\Main::isPaypalForMarketplacesEnabled()) {
            $this->silent = true;
            $this->setSuppressOutput(true);

            if (\XLite\Core\Request::getInstance()->method === Paypal\Main::PP_METHOD_PFM
                && Paypal\Main::isPaypalForMarketplacesEnabled()
            ) {
                $paymentMethod = $this->getPaypalForMarketplacesPaymentMethod();

            } else {
                $paymentMethod = $this->getExpressCheckoutPaymentMethod();
            }

            $this->getCart()->setPaymentMethod($paymentMethod);

            if (!$this->getCart()->getPaymentStatus()) {
                $this->getCart()->setPaymentStatus(\XLite\Model\Order\Status\Payment::STATUS_QUEUED);
            }

            $this->prepareProfile();
            $this->updateCart();

            \XLite\Core\Session::getInstance()->ec_type
                = Paypal\Model\Payment\Processor\ExpressCheckout::EC_TYPE_SHORTCUT;

            $transaction = $this->getCart()->getFirstOpenPaymentTransaction();

            $processor = $paymentMethod->getProcessor();
            $token     = $processor->doSetExpressCheckout($paymentMethod, $transaction);

            if (null !== $token) {
                \XLite\Core\Session::getInstance()->ec_token           = $token;
                \XLite\Core\Session::getInstance()->ec_date            = \XLite\Core\Converter::time();
                \XLite\Core\Session::getInstance()->ec_payer_id        = null;
                \XLite\Core\Session::getInstance()->ec_ignore_checkout = (bool) \XLite\Core\Request::getInstance()->ignoreCheckout;


                echo json_encode(['token' => $token]);
            } else {
                if (\XLite\Core\Request::getInstance()->inContext) {
                    \XLite\Core\Session::getInstance()->cancelUrl         = \XLite\Core\Request::getInstance()->cancelUrl;
                    \XLite\Core\Session::getInstance()->inContextRedirect = true;
                    $this->setReturnURL($this->buildURL('checkout'));
                }

                echo json_encode(['error' => $processor->getErrorMessage() ?: 'Failure to get PayPal token']);
            }
        }
    }

    /**
     * doExpressCheckoutReturn
     *
     * @return void
     */
    protected function doActionExpressCheckoutReturn()
    {
        $request = \XLite\Core\Request::getInstance();
        $cart    = $this->getCart();

        Paypal\Main::addLog('doExpressCheckoutReturn()', $request->getData());

        $checkoutAction = false;

        if (isset($request->cancel)) {
            \XLite\Core\Session::getInstance()->ec_token    = null;
            \XLite\Core\Session::getInstance()->ec_date     = null;
            \XLite\Core\Session::getInstance()->ec_payer_id = null;
            \XLite\Core\Session::getInstance()->ec_type     = null;

            $cart->unsetPaymentMethod();

            \XLite\Core\TopMessage::addWarning('Express Checkout process stopped.');

        } elseif (!isset($request->token) || $request->token !== \XLite\Core\Session::getInstance()->ec_token) {
            \XLite\Core\TopMessage::getInstance()->addError('Wrong token of Express Checkout. Please try again. If the problem persists, contact the administrator.');

        } elseif (!isset($request->PayerID)) {
            \XLite\Core\TopMessage::getInstance()->addError('PayerID value was not returned by PayPal. Please try again. If the problem persists, contact the administrator.');

        } else {
            // Express Checkout shortcut flow processing

            \XLite\Core\Session::getInstance()->ec_type
                = Paypal\Model\Payment\Processor\ExpressCheckout::EC_TYPE_SHORTCUT;

            \XLite\Core\Session::getInstance()->ec_payer_id = $request->PayerID;

            if (\XLite\Core\Request::getInstance()->method === Paypal\Main::PP_METHOD_PFM
                && Paypal\Main::isPaypalForMarketplacesEnabled()
            ) {
                $paymentMethod = $this->getPaypalForMarketplacesPaymentMethod();

            } else {
                $paymentMethod = $this->getExpressCheckoutPaymentMethod();
            }

            $processor = $paymentMethod->getProcessor();

            /** @var \XLite\Module\CDev\Paypal\Model\Payment\Processor\ExpressCheckout|\XLite\Module\CDev\Paypal\Model\Payment\Processor\ExpressCheckoutMerchantAPI $processor */
            $buyerData = $processor->doGetExpressCheckoutDetails($paymentMethod);

            if (empty($buyerData)) {
                \XLite\Core\TopMessage::getInstance()->addError('Your address data was not received from PayPal. Please try again. If the problem persists, contact the administrator.');

            } else {
                // Fill the cart with data received from Paypal
                $this->requestData = $this->prepareBuyerData($processor, $buyerData);

                if (!\XLite\Core\Auth::getInstance()->isLogged()) {
                    $this->updateProfile();
                }

                $modifier = $cart->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
                if ($modifier && $modifier->canApply()) {
                    $this->requestData['billingAddress'] = $this->requestData['shippingAddress'];
                    $this->requestData['same_address']   = true;

                    $this->updateShippingAddress();

                    $this->updateBillingAddress();
                }

                $this->setCheckoutAvailable();

                $this->updateCart();

                if (\XLite\Core\Session::getInstance()->ec_ignore_checkout) {
                    $this->doActionCheckout();
                } else {
                    $params = [
                        'ec_returned' => true,
                    ];
                    $this->setReturnURL($this->buildURL('checkout', '', $params));
                }
                \XLite\Core\Session::getInstance()->ec_ignore_checkout = null;

                $checkoutAction = true;
            }
        }

        if (!$checkoutAction) {
            $this->setReturnURL(\XLite\Core\Request::getInstance()->cancelUrl ?: $this->buildURL('checkout'));
        }
    }

    /**
     * Do payment
     *
     * @return void
     */
    protected function doPayment()
    {
        $isEC = (Paypal\Model\Payment\Processor\ExpressCheckout::EC_TYPE_SHORTCUT == \XLite\Core\Session::getInstance()->ec_type);

        $this->setHardRedirect(
            $this->isReturnedAfterExpressCheckout() || $this->isReturnedAfterPaypalCommercePlatform()
        );

        parent::doPayment();

        /** @var \XLite\Module\CDev\Paypal\Model\Order $cart */
        $cart = $this->getCart();

        if ($isEC && $cart->isExpressCheckout($cart->getPaymentMethod())) {
            $url = $this->returnURL;
            if (preg_match('/target=checkout(Success|Failed)/', $url, $m) && !preg_match('/order_number=|order_id=/', $url)) {
                $cart = $this->getCart();
                $this->setReturnURL(
                    $this->buildURL(
                        'checkout' . $m[1],
                        '',
                        $cart->getOrderNumber()
                            ? ['order_number' => $cart->getOrderNumber()]
                            : ['order_id' => $cart->getOrderId()]
                    )
                );
            }
        }
    }

    /**
     * Set up ec_type flag to 'mark' value if payment method selected on checkout
     *
     * @return void
     */
    protected function doActionPayment()
    {
        \XLite\Core\Session::getInstance()->ec_type
            = Paypal\Model\Payment\Processor\ExpressCheckout::EC_TYPE_MARK;

        parent::doActionPayment();
    }

    /**
     * Translate array of data received from Paypal to the array for updating cart
     *
     * @param \XLite\Model\Payment\Base\Processor $processor  Payment processor
     * @param array                               $paypalData Array of customer data received from Paypal
     *
     * @return array
     */
    protected function prepareBuyerData($processor, $paypalData)
    {
        $data = $processor->prepareBuyerData($paypalData);

        $profile = $this->getProfile();

        if (!$profile && $this->getCart()) {
            $profile = $this->getCart()->getProfile();
        }

        if (
            !\XLite\Core\Auth::getInstance()->isLogged()
            && !$profile->getLogin()
        ) {
            $data += [
                'email'          => str_replace(' ', '+', $paypalData['EMAIL']),
                'create_profile' => false,
            ];
        }

        if (isset($data['shippingAddress'])
            && $profile
            && $profile->getShippingAddress()
        ) {
            $data['shippingAddress'] = array_filter(
                $data['shippingAddress']
            );

            $data['shippingAddress'] = array_replace(
                $profile->getShippingAddress()->serialize(),
                $data['shippingAddress']
            );
        }

        return $data;
    }

    /**
     * Get Express Checkout payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function getExpressCheckoutPaymentMethod()
    {
        $serviceName = \XLite\Core\Request::getInstance()->paypalCredit
            ? Paypal\Main::PP_METHOD_PC
            : Paypal\Main::PP_METHOD_EC;

        return Paypal\Main::getPaymentMethod($serviceName);
    }

    /**
     * Get Express Checkout payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function getPaypalForMarketplacesPaymentMethod()
    {
        $serviceName = Paypal\Main::PP_METHOD_PFM;

        return Paypal\Main::getPaymentMethod($serviceName);
    }

    /**
     * Checkout
     * TODO: to revise
     *
     * @return void
     */
    protected function doActionCheckout()
    {
        parent::doActionCheckout();

        $cart           = $this->getCart();
        $paymentMethods = $cart->getPaymentMethod();

        if ($paymentMethods) {
            $processor = $paymentMethods->getProcessor();

            if ($processor instanceof \XLite\Module\CDev\Paypal\Model\Payment\Processor\PayflowTransparentRedirect
                && $this->getReturnURL() === $this->buildURL('checkoutPayment')
            ) {
                $this->set('silent', true);
            }
        }
    }

    /**
     * Update profile
     *
     * @return void
     */
    protected function doActionUpdateProfile()
    {
        parent::doActionUpdateProfile();

        \XLite\Core\Event::updatePaypalTransparentRedirect([]);
    }
}
