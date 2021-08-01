<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\Controller\Customer;

/**
 * PayPal powered by Braintree module's controller for checkout 
 *
 */
 class Checkout extends \XLite\Module\XC\CanadaPost\Controller\Customer\Checkout implements \XLite\Base\IDecorator 
{
    /**
     * Save nonce in session and the details to profile
     *
     * @return void
     */
    protected function doActionContinuePaypal()
    {
        $request = \XLite\Core\Request::getInstance();

        \XLite\Core\Session::getInstance()->braintree_paypal_nonce = strval($request->nonce);

        $details = @json_decode(strval($request->details), true);

        if (
            !empty($details)
            && ($this->isAnonymous())
        ) {
            
            if ($this->getCart()->getProfile()) {

                $profile = $this->getCart()->getProfile();
                $address = $profile->getShippingAddress();

                if (!$profile->getLogin()) {
                    $profile->setLogin($details['email']);
                }

            } else {

                $profile = new \XLite\Model\Profile;
                $profile->setLogin($details['email']);
                $profile->persist();
                $address = false;
            }

            if (!$address) {

                $address = new \XLite\Model\Address;
                $address->setIsShipping(true);
                $address->persist();
                $profile->setShippingAddress($address);

            }

            if (!$profile->getBillingAddress()) {
                $address->setIsBilling(true);
                $profile->setBillingAddress($address);
            }

            if (!empty($details['firstName'])) {
                $profile->setFirstname($details['firstName']);
                $address->setFirstname($details['firstName']);
            }

            if (!empty($details['lastName'])) {
                $profile->setLastname($details['lastName']);
                $address->setLastname($details['lastName']);
            }

            if (!empty($details['shippingAddress']['line1'])) {
                $address->setStreet($details['shippingAddress']['line1']);
            }

            if (!empty($details['shippingAddress']['city'])) {
                $address->setCity($details['shippingAddress']['city']);
            }

            if (!empty($details['shippingAddress']['postalCode'])) {
                $address->setZipcode($details['shippingAddress']['postalCode']);
            }

            if (!empty($details['shippingAddress']['countryCode'])) {

                $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->findOneByCode($details['shippingAddress']['countryCode']);
                $address->setCountry($country);

                if (!empty($details['shippingAddress']['state'])) {

                    $state = \XLite\Core\Database::getRepo('XLite\Model\State')->findOneByCountryAndCode(
                        $details['shippingAddress']['countryCode'],
                        $details['shippingAddress']['state']
                    );

                    $address->setState($state);
                }
            }

            \XLite\Core\Database::getEM()->flush();

            \XLite\Core\Session::getInstance()->same_address = true;

            $this->setCheckoutAvailable();

            $this->updateCart();
        }

        $this->setHardRedirect();
        $this->setReturnURL($this->buildURL('checkout'));
        $this->doRedirect();
    }

}
