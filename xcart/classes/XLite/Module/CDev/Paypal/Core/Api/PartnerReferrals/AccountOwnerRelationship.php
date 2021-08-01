<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-account_owner_relationship
 *
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Name name
 * @property string                                                   relation
 * @property string                                                   country_code_of_nationality
 */
class AccountOwnerRelationship extends PayPalModel
{
    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Name $name
     *
     * @return AccountOwnerRelationship
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * Valid Values: ["MOTHER"]
     *
     * @param string $relation
     *
     * @return AccountOwnerRelationship
     */
    public function setRelation($relation)
    {
        $this->relation = $relation;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCodeOfNationality()
    {
        return $this->country_code_of_nationality;
    }

    /**
     * @param string $country_code_of_nationality
     *
     * @return AccountOwnerRelationship
     */
    public function setCountryCodeOfNationality($country_code_of_nationality)
    {
        $this->country_code_of_nationality = $country_code_of_nationality;

        return $this;
    }
}
