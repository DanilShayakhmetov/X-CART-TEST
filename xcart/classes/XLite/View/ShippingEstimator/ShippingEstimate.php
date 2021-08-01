<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ShippingEstimator;

/**
 * Shipping estimator
 *
 * @ListChild (list="center")
 */
class ShippingEstimate extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'shipping_estimate';

        return $result;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'shopping_cart/shipping_estimator/body.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getModifier();
    }

    /**
     * Get countries list
     *
     * @return array(\XLite\Model\Country)
     */
    protected function getCountries()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Country')
            ->findByEnabled(true);
    }

    /**
     * Return true if state selector field is visible
     *
     * @return boolean
     */
    protected function isStateFieldVisible()
    {
        return $this->checkStateFieldVisibility();
    }

    /**
     * Return true if custom_state input field is visible
     *
     * @return boolean
     */
    protected function isCustomStateFieldVisible()
    {
        return $this->checkStateFieldVisibility(true);
    }

    /**
     * Common method to check visibility of state fields
     *
     * @param boolean $isCustom Flag: true - check for custom_state field visibility, false - state selector field
     *
     * @return boolean
     */
    protected function checkStateFieldVisibility($isCustom = false)
    {
        $result = false;

        // hasField() method is defined in controller XLite\Controller\Customer\ShippingEstimate
        if ($this->hasField('state_id')) {
            if ($this->hasField('country_code')) {
                $result = true;

            } else {
                $address = $this->getAddress();

                $country = !empty($address['country'])
                    ? \XLite\Core\Database::getRepo('XLite\Model\Country')->findOneByCode($address['country'])
                    : \XLite\Model\Address::getDefaultFieldValue('country');

                $result = $isCustom
                    ? !$country || !$country->hasStates()
                    : $country && $country->hasStates();
            }
        }

        return $result;
    }

    /**
     * Get selected country code
     *
     * @return string
     */
    protected function getCountryCode()
    {
        $result = 'US';

        $address = $this->getAddress();
        if ($address && isset($address['country'])) {
            $result = $address['country'];

        } elseif (\XLite\Model\Address::getDefaultFieldValue('country')) {
            $result = \XLite\Model\Address::getDefaultFieldValue('country')->getCode();
        }

        return $result;
    }

    /**
     * Get state
     *
     * @return \XLite\Model\State
     */
    protected function getState()
    {
        $address = $this->getAddress();

        $state = null;

        // From getDestinationAddress()
        if ($address && !empty($address['state'])) {
            if (is_integer($address['state'])) {
                $state = \XLite\Core\Database::getRepo('XLite\Model\State')->find($address['state']);

            } elseif (!empty($address['country'])) {
                $state = \XLite\Core\Database::getRepo('XLite\Model\State')->findOneByCountryAndCode($address['country'], $address['state']);
            }

        } elseif ($this->getCart()->getProfile()
            && $this->getCart()->getProfile()->getShippingAddress()
            && $this->getCart()->getProfile()->getShippingAddress()->getState()
        ) {
            // From shipping address
            $state = $this->getCart()->getProfile()->getShippingAddress()->getState();

        } elseif (!$address
            && \XLite\Model\Address::getDefaultFieldValue('custom_state')
        ) {
            // From config
            $state = new \XLite\Model\State();
            $state->setState(\XLite\Model\Address::getDefaultFieldValue('custom_state'));

        }

        return $state;
    }

    /**
     * Get state
     *
     * @return \XLite\Model\State
     */
    protected function getOtherState()
    {
        $state = null;

        $address = $this->getAddress();

        if (isset($address) && isset($address['custom_state'])) {
            $state = $address['custom_state'];

        } elseif ($this->getCart()->getProfile()
            && $this->getCart()->getProfile()->getShippingAddress()
            && $this->getCart()->getProfile()->getShippingAddress()->getCustomState()
        ) {
            // From shipping address
            $state = $this->getCart()->getProfile()->getShippingAddress()->getCustomState();
        }

        return $state;
    }

    /**
     * Get ZIP code
     *
     * @return string
     */
    protected function getZipcode()
    {
        $address = $this->getAddress();

        return ($address && isset($address['zipcode']))
            ? $address['zipcode']
            : '';
    }

    /**
     * Get City
     *
     * @return string
     */
    protected function getCity()
    {
        $address = $this->getAddress();

        return ($address && isset($address['city']))
            ? $address['city']
            : '';
    }

    /**
     * Get address type code
     *
     * @return string
     */
    protected function getType()
    {
        $address = $this->getAddress();

        return ($address && isset($address['type']))
            ? $address['type']
            : '';
    }

    /**
     * Check - shipping is estimate or not
     *
     * @return boolean
     */
    protected function isEstimate()
    {
        return $this->getAddress()
            && $this->getCart()->getProfile()
            && $this->getCart()->getProfile()->getShippingAddress();
    }

    /**
     * Returns shipping rates
     *
     * @return boolean
     */
    protected function hasRates()
    {
        return (boolean) $this->getModifier()->getRates();
    }

    /**
     * Check field is required
     *
     * @param $fieldName
     * @return bool
     */
    public function isFieldRequired($fieldName)
    {
        $field = \XLite\Core\Database::getRepo('XLite\Model\AddressField')->findOneBy([
            'serviceName' => $fieldName,
            'enabled' => true,
        ]);

        return $field && $field->getRequired();
    }
}
