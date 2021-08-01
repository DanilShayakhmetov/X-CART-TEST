<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator\Pair;

/**
 * Country-state validator
 */
class CountryState extends \XLite\Core\Validator\Pair\APair
{
    /**
     * Field names
     */
    const FIELD_COUNTRY        = 'country_code';
    const FIELD_STATE          = 'state_id';
    const FIELD_CUSTOM_STATE   = 'custom_state';

    /**
     * @var array
     */
    protected $addressFields = [];

    /**
     * Validate
     *
     * @param mixed $data Data
     *
     * @return void
     * @throws \XLite\Core\Validator\Exception
     */
    public function validate($data)
    {
        // Check country
        if (!isset($data[static::FIELD_COUNTRY])) {

            if (!$this->isFieldAvailable(static::FIELD_COUNTRY)) {
                $defaultCountry = \XLite\Model\Address::getDefaultFieldValue('country');
                $data[static::FIELD_COUNTRY] = $defaultCountry ? $defaultCountry->getCode() : null;
            }

            if (empty($data[static::FIELD_COUNTRY])) {
                throw $this->throwError('Country is not defined');
            }
        }

        if (!empty($data[static::FIELD_COUNTRY]) || $this->isFieldRequired(static::FIELD_COUNTRY)) {
            $countryCodeValidator = new \XLite\Core\Validator\Pair\Simple;
            $countryCodeValidator->setName(static::FIELD_COUNTRY);
            $countryCodeValidator->setValidator(
                new \XLite\Core\Validator\String\ObjectId\Country(true)
            );
            $countryCodeValidator->validate($data);
        }

        // Check state
        if (!empty($data[static::FIELD_STATE]) && !empty($data[static::FIELD_COUNTRY])) {
            $stateValidator = new \XLite\Core\Validator\Pair\Simple;
            $stateValidator->setName(static::FIELD_STATE);
            $stateValidator->setValidator(
                new \XLite\Core\Validator\String\ObjectId\State(true)
            );
            $stateValidator->validate($data);
        }

        $data = $this->sanitize($data);

        if ($data['country']) {
            if (empty($data['state'])
                && $this->isFieldAvailable(static::FIELD_STATE)
                && $data['country']
                && $data['country']->hasStates()
                && !$data['country']->isForcedCustomState()
            ) {
                throw $this->throwError('State is not defined');

            } elseif ($data['state']
                && $data['state']->getCountry()
                && $data['state']->getCountry()->getCode() != $data['country']->getCode()
            ) {
                throw $this->throwError('Country has not specified state');
            }
        }
    }

    /**
     * Sanitize
     *
     * @param mixed $data Daa
     *
     * @return mixed
     */
    public function sanitize($data)
    {
        // Check country
        if (!isset($data[static::FIELD_COUNTRY]) && !$this->isFieldAvailable(static::FIELD_COUNTRY)
        ) {
            $defaultCountry = \XLite\Model\Address::getDefaultFieldValue('country');
            $data[static::FIELD_COUNTRY] = $defaultCountry ? $defaultCountry->getCode() : null;
        }

        // Get country
        $country = null;
        if (!empty($data[static::FIELD_COUNTRY]) || $this->isFieldRequired(static::FIELD_COUNTRY)) {
            $countryCodeValidator = new \XLite\Core\Validator\Pair\Simple;
            $countryCodeValidator->setName(static::FIELD_COUNTRY);
            $countryCodeValidator->setValidator(
                new \XLite\Core\Validator\String\ObjectId\Country(true)
            );

            $country = $countryCodeValidator->getValidator()->sanitize($data[static::FIELD_COUNTRY]);
        }

        // Get state
        if (
            $country
            && $country->hasStates()
            && !$country->isForcedCustomState()
            && isset($data[static::FIELD_STATE])
        ) {
            $stateValidator = new \XLite\Core\Validator\String\ObjectId\State(true);
            $state = $stateValidator->sanitize($data[static::FIELD_STATE]);

        } elseif (!empty($data[static::FIELD_CUSTOM_STATE])) {
            $state = new \XLite\Model\State;
            $state->setState($data[static::FIELD_CUSTOM_STATE]);
            $state->setCountry($country);
            $data[static::FIELD_STATE] = $data[static::FIELD_CUSTOM_STATE];

        } else {
            $state = null;
        }

        return array(
            'country'             => $country,
            'state'               => $state,
            static::FIELD_COUNTRY => $data[static::FIELD_COUNTRY],
            static::FIELD_STATE   => ($state && $country && $country->hasStates()) ? $data[static::FIELD_STATE] : null,
        );
    }

    /**
     * Check if the enabled address field with the given name exists
     *
     * @param string $fieldName Field name
     *
     * @return boolean
     */
    protected function isFieldAvailable($fieldName)
    {
        $addressField = $this->getAddressField($fieldName);

        return $addressField ? $addressField->getEnabled() : false;
    }

    /**
     * Check if the enabled address field with the given name exists and required
     *
     * @param string $fieldName Field name
     *
     * @return boolean
     */
    protected function isFieldRequired($fieldName)
    {
        $addressField = $this->getAddressField($fieldName);

        return $addressField ? ($addressField->getEnabled() && $addressField->getRequired()) : false;
    }

    /**
     * Get address field by name
     *
     * @param string $fieldName Field name
     *
     * @return \XLite\Model\AddressField
     */
    protected function getAddressField($fieldName)
    {
        if (!isset($this->addressFields[$fieldName])) {
            $this->addressFields[$fieldName] = \XLite\Core\Database::getRepo('XLite\Model\AddressField')->findOneBy(
                array(
                    'serviceName' => $fieldName,
                )
            );
        }

        return $this->addressFields[$fieldName];
    }
}
