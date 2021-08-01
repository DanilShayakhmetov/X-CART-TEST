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
 * Checkout controller
 */
 class Checkout extends \XLite\Controller\Customer\CheckoutAbstract implements \XLite\Base\IDecorator
{
    /**
     * Send event in case if Anonymous customer intended to create profile
     *
     * @throws \Exception
     */
    protected function updateAnonymousProfile()
    {
        parent::updateAnonymousProfile();

        $createProfile = (
            \XLite\Core\Session::getInstance()->order_create_profile
            && \XLite\Core\Session::getInstance()->createProfilePassword
        );

        \XLite\Core\Event::xpaymentsAnonymousRegister(
            array(
                'value' => $createProfile,
            )
        );
    }

    /**
     * Checks if customer is Anonymous and didn't choose to create profile
     * or cart has subscription items
     */
    public function isHideSaveCardCheckbox()
    {
        $anonymous = $this->isAnonymous()
           && (
               !\XLite\Core\Session::getInstance()->order_create_profile
               || !\XLite\Core\Session::getInstance()->createProfilePassword
           );

        $cartHasSubscriptions = $this->getCart()->hasXpaymentsSubscriptionItems();

        return $anonymous || $cartHasSubscriptions;
    }

    /**
     * Returns X-Payments Cloud payment method
     */
    public function getXpaymentsMethod()
    {
        return XPaymentsCloud::getPaymentMethod();
    }

    /**
     * Checks if current method is Apple Pay
     */
    public function isXpaymentsApplePaySelected()
    {
        return ($this->getCart()->getPaymentMethod()->isXpaymentsApplePay());
    }

    /**
     * Checks if Apple Pay method exists, but not selected
     */
    public function isXpaymentsApplePayEnabled()
    {
        return (XPaymentsCloud::getApplePayMethod()->isEnabled());
    }

    /**
     * Returns xpaymentsCustomerId for current profile (if available)
     */
    public function getXpaymentsCustomerId()
    {
        return ($this->getCart()->getProfile())
            ? $this->getCart()->getProfile()->getXpaymentsCustomerId()
            : '';
    }

    /**
     * Sends updated total on cartUpdate (not only the difference)
     *
     * @return boolean
     */
    protected function assembleEvent()
    {
        $result = parent::assembleEvent();
        if ($result) {
            \XLite\Core\Event::xpaymentsTotalUpdate(
                array(
                    'total' => $this->getCart()->getTotal(),
                    'currency' => $this->getCart()->getCurrency()->getCode(),
                )
            );
        }
        return $result;
    }

    /**
     * Translate array of data received from Apple Pay to the array suitable for X-Cart
     *
     * @param array  $contact Array of customer data received from Apple Pay
     * @param string $type Billing or shipping
     * @param \XLite\Model\Profile $profile
     *
     * @return array
     */
    protected function convertApplePayContactToAddress($contact, $type = \XLite\Model\Address::SHIPPING, $profile = null)
    {
        $countryCode = $contact['countryCode'];
        $country = \XLite\Core\Database::getRepo('XLite\Model\Country')
            ->findOneByCode($countryCode);

        $stateCode = $contact['administrativeArea'];
        $state = ($country && $stateCode)
            ? \XLite\Core\Database::getRepo('XLite\Model\State')
                ->findOneByCountryAndState($country->getCode(), mb_strtoupper($stateCode, 'UTF-8'))
            : null;

        $data = [
            'name' => $contact['givenName'] . (!empty($contact['familyName']) ? ' ' . $contact['familyName'] : ''),
            'street' => implode(' ', $contact['addressLines']),
            'country_code' => $countryCode,
            'country' => $country ?: '',
            'state_id' => $state ? $state->getStateId() : null,
            'state' => $state ?: (string) $stateCode,
            'custom_state' => $state ? $state->getState() : (string) $stateCode,
            'city' => $contact['locality'],
            'zipcode' => $contact['postalCode'],
            'phone' => isset($contact['phoneNumber']) ? $contact['phoneNumber'] : '',
        ];

        $func = 'get'  . (\XLite\Model\Address::SHIPPING == $type ? 'Shipping' : 'Billing') . 'Address';

        if (
            $profile
            && $profile->$func()
        ) {
            $data = array_filter($data);
            $data = array_replace(
                $profile->$func()->serialize(),
                $data
            );
        }

        return $data;

    }

    /**
     * Translate array of data received from Apple Pay to the array for updating cart
     *
     * @param array                               $data Array of customer data received from Apple Pay
     *
     * @return array
     */
    protected function prepareApplePayContactData($data)
    {
        $profile = $this->getProfile();

        if (!$profile && $this->getCart()) {
            $profile = $this->getCart()->getProfile();
        }

        $result = [
            'same_address' => false,
            'shippingAddress' => $this->convertApplePayContactToAddress($data['shippingContact'], \XLite\Model\Address::SHIPPING, $profile),
            'billingAddress' => $this->convertApplePayContactToAddress($data['billingContact'], \XLite\Model\Address::BILLING, $profile),
        ];

        if (
            !\XLite\Core\Auth::getInstance()->isLogged()
            && !$profile->getLogin()
            && (
                !empty($data['shippingContact']['emailAddress'])
                || !empty($data['billingContact']['emailAddress'])
            )
        ) {
            $result += [
                'email'          => $data['shippingContact']['emailAddress'] ?: $data['billingContact']['emailAddress'],
                'create_profile' => false,
            ];
        }

        return $result;
    }

    /**
     * Get missing fields list with Apple Pay codenames
     *
     * @param \XLite\Model\Profile $profile Customer profile
     * @param string $type Shipping or Billing
     *
     * @return array
     */
    protected function getApplePayMissingAddressFields($profile, $type = \XLite\Model\Address::SHIPPING)
    {
        if (\XLite\Model\Address::SHIPPING == $type) {
            $fields = $profile->getShippingAddress()->getRequiredEmptyFields($type);
        } else {
            $fields = $profile->getBillingAddress()->getRequiredEmptyFields($type);
        }

        $errorFields = [];
        foreach ($fields as $field) {
            switch ($field) {
                case 'state':
                case 'state_id':
                case 'custom_state':
                    $errorFields[] = 'administrativeArea';
                    break;
                case 'name':
                    $errorFields[] = 'givenName';
                    $errorFields[] = 'familyName';
                    break;
                case 'street':
                    $errorFields[] = 'addressLines';
                    break;
                case 'country':
                    $errorFields[] = 'countryCode';
                    break;
                case 'city':
                    $errorFields[] = 'locality';
                    break;
                case 'zipcode':
                    $errorFields[] = 'postalCode';
                    break;
                case 'phone':
                    $errorFields[] = 'phoneNumber';
                    break;
            }
        }
        return $errorFields;

    }

    /**
     * Returns list of address errors for Apple Pay (if any)
     *
     * @param \XLite\Model\Profile $profile Customer profile
     *
     * @return array
     */
    protected function checkApplePayAddressErrors($profile)
    {
        $errors = [];

        foreach ([\XLite\Model\Address::SHIPPING, \XLite\Model\Address::BILLING] as $type) {
            $address = (\XLite\Model\Address::SHIPPING == $type) ? $profile->getShippingAddress() : $profile->getBillingAddress();
            $label = (\XLite\Model\Address::SHIPPING == $type) ? 'shipping' : 'billing';

            if (!$address->checkAddress()) {
                $errors[] = (object)[
                    'code' => $label . 'ContactInvalid',
                    'contactField' => 'postalAddress',
                    'message' => static::t(ucfirst($label) . ' address is invalid')
                ];
            } elseif (!$address->isCompleted($type)) {
                foreach ($this->getApplePayMissingAddressFields($profile, $type) as $appleField) {
                    $errors[] = (object)[
                        'code' => $label . 'ContactInvalid',
                        'contactField' => $appleField,
                        'message' => static::t('One or more required fields are empty')
                    ];
                }
            }
        }

        return $errors;
    }

    /**
     * Sets final customer address before proceeding with payment
     *
     * @throws \Exception
     */
    protected function doActionXpaymentsApplePayPrepare()
    {
        $this->silent = true;
        $this->setSuppressOutput(true);

        $request = \XLite\Core\Request::getInstance();

        $paymentMethod = XPaymentsCloud::getApplePayMethod();
        $this->getCart()->setPaymentMethod($paymentMethod);

        // Fill the address with data received from Apple Pay
        $this->requestData = $this->prepareApplePayContactData($request->getData());

        if (!\XLite\Core\Auth::getInstance()->isLogged()) {
            $this->updateProfile();
        }

        $this->updateShippingAddress();
        $this->updateBillingAddress();

        $this->setCheckoutAvailable();

        $this->updateCart(true);

        $profile = $this->getProfile();
        if (!$profile && $this->getCart()) {
            $profile = $this->getCart()->getProfile();
        }

        $this->setPureAction(true);
        $this->setInternalRedirect(false);

        $result = new \StdClass();
        $result->errors = [];

        if ($this->valid) {
            $result->errors += $this->checkApplePayAddressErrors($profile);
        } else {
            $result->errors[] = (object)[
                'code' => 'addressUnserviceable',
                'contactField' => 'postalAddress',
                'message' => 'Failed to process address'
            ];
        }

        echo json_encode($result);
    }

    /**
     * Prepares data and runs default checkout
     *
     * @return void
     */
    protected function doActionXpaymentsApplePayCheckout()
    {
        $paymentMethod = XPaymentsCloud::getApplePayMethod();

        $this->getCart()->setPaymentMethod($paymentMethod);

        $this->doActionCheckout();
    }

    /**
     * Fix unsetting of anonymous cart id from session for Buy With Apple Pay
     *
     * @param boolean $fullProcess Full process or not OPTIONAL
     *
     * @return void
     */
    public function processSucceed($fullProcess = true)
    {
        if (\XLite\Core\Request::getInstance()->xpaymentsBuyWithApplePay) {
            $savedOrderId = \XLite\Core\Session::getInstance()->order_id;
        }

        parent::processSucceed($fullProcess);

        if (\XLite\Core\Request::getInstance()->xpaymentsBuyWithApplePay) {
            if (!empty($savedOrderId)) {
                // anonymous cart id was cleared by parent call but it uses
                // unsetBatch() which doesn't update cache and thus
                // we need to "unset" it again first to be able to set it
                unset(\XLite\Core\Session::getInstance()->order_id);
                \XLite\Core\Session::getInstance()->order_id = $savedOrderId;
            }

            // Remove item bought using Apple Pay from real cart
            // if it also contains that item - to follow Apple guidelines
            $item = $this->getCart()->getItems()->first();
            $realCart = \XLite\Model\Cart::getInstance(false);
            $itemInRealCart = $realCart->getItemByItem($item);
            if ($itemInRealCart) {
                $realCart->getItems()->removeElement($itemInRealCart);
                \XLite\Core\Database::getEM()->remove($itemInRealCart);
                \XLite\Core\Database::getEM()->flush();
            }
        }
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

    /**
     * Check checkout action accessibility
     *
     * @return boolean
     */
    public function checkCheckoutAction()
    {
        return
            (
                'xpayments_apple_pay_checkout' == \XLite\Core\Request::getInstance()->action
                && $this->getCart()->getPaymentMethod()
                && $this->getCart()->getPaymentMethod()->isXpayments()
            )
            ? true
            : parent::checkCheckoutAction();
    }
}
