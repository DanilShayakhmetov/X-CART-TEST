<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-identity_document
 *
 * @property string type
 * @property string value
 * @property string partial_value
 * @property string issuer_country_code
 */
class IdentityDocument extends PayPalModel
{
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Valid Values: ["SOCIAL_SECURITY_NUMBER", "EMPLOYMENT_IDENTIFICATION_NUMBER", "TAX_IDENTIFICATION_NUMBER",
     * "PASSPORT_NUMBER", "PENSION_FUND_ID", "MEDICAL_INSURANCE_ID", "CNPJ", "CPF"]
     *
     * @param string $type
     *
     * @return IdentityDocument
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return IdentityDocument
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getPartialValue()
    {
        return $this->partial_value;
    }

    /**
     * @param string $partial_value
     *
     * @return IdentityDocument
     */
    public function setPartialValue($partial_value)
    {
        $this->partial_value = $partial_value;

        return $this;
    }

    /**
     * @return string
     */
    public function getIssuerCountryCode()
    {
        return $this->issuer_country_code;
    }

    /**
     * @param string $issuer_country_code
     *
     * @return IdentityDocument
     */
    public function setIssuerCountryCode($issuer_country_code)
    {
        $this->issuer_country_code = $issuer_country_code;

        return $this;
    }
}
