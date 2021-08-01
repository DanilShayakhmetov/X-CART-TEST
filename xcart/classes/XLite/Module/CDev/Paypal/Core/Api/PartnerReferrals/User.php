<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-user
 *
 * @property string                                                                          customer_type
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\PersonDetails               person_details
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\BusinessDetails             business_details
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\FinancialInstrumentData     financial_instrument_data
 * @property string                                                                          preferred_language_code
 * @property string                                                                          primary_currency_code
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\AccountIdentifier           referral_user_payer_id
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\PartnerSpecificIdentifier[] partner_specific_identifiers
 */
class User extends PayPalModel
{
    /**
     * @return string
     */
    public function getCustomerType()
    {
        return $this->customer_type;
    }

    /**
     * @param string $customer_type
     *
     * @return User
     */
    public function setCustomerType($customer_type)
    {
        $this->customer_type = $customer_type;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\PersonDetails
     */
    public function getPersonDetails()
    {
        return $this->person_details;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\PersonDetails $person_details
     *
     * @return User
     */
    public function setPersonDetails($person_details)
    {
        $this->person_details = $person_details;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\BusinessDetails
     */
    public function getBusinessDetails()
    {
        return $this->business_details;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\BusinessDetails $business_details
     *
     * @return User
     */
    public function setBusinessDetails($business_details)
    {
        $this->business_details = $business_details;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\FinancialInstrumentData
     */
    public function getFinancialInstrumentData()
    {
        return $this->financial_instrument_data;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\FinancialInstrumentData $financial_instrument_data
     *
     * @return User
     */
    public function setFinancialInstrumentData($financial_instrument_data)
    {
        $this->financial_instrument_data = $financial_instrument_data;

        return $this;
    }

    /**
     * @return string
     */
    public function getPreferredLanguageCode()
    {
        return $this->preferred_language_code;
    }

    /**
     * @param string $preferred_language_code
     *
     * @return User
     */
    public function setPreferredLanguageCode($preferred_language_code)
    {
        $this->preferred_language_code = $preferred_language_code;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrimaryCurrencyCode()
    {
        return $this->primary_currency_code;
    }

    /**
     * @param string $primary_currency_code
     *
     * @return User
     */
    public function setPrimaryCurrencyCode($primary_currency_code)
    {
        $this->primary_currency_code = $primary_currency_code;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\AccountIdentifier
     */
    public function getReferralUserPayerId()
    {
        return $this->referral_user_payer_id;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\AccountIdentifier $referral_user_payer_id
     *
     * @return User
     */
    public function setReferralUserPayerId($referral_user_payer_id)
    {
        $this->referral_user_payer_id = $referral_user_payer_id;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\PartnerSpecificIdentifier[]
     */
    public function getPartnerSpecificIdentifiers()
    {
        return $this->partner_specific_identifiers;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\PartnerSpecificIdentifier[] $partner_specific_identifiers
     *
     * @return User
     */
    public function setPartnerSpecificIdentifiers($partner_specific_identifiers)
    {
        $this->partner_specific_identifiers = $partner_specific_identifiers;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\PartnerSpecificIdentifier $partner_specific_identifier
     *
     * @return User
     */
    public function addPartnerSpecificIdentifier($partner_specific_identifier)
    {
        if (!$this->getPartnerSpecificIdentifiers()) {

            return $this->setPartnerSpecificIdentifiers([$partner_specific_identifier]);
        }

        return $this->setPartnerSpecificIdentifiers(
            array_merge($this->getPartnerSpecificIdentifiers(), [$partner_specific_identifier])
        );
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\PartnerSpecificIdentifier $partner_specific_identifier
     *
     * @return User
     */
    public function removePartnerSpecificIdentifier($partner_specific_identifier)
    {
        return $this->setPartnerSpecificIdentifiers(
            array_diff($this->getPartnerSpecificIdentifiers(), [$partner_specific_identifier])
        );
    }
}
