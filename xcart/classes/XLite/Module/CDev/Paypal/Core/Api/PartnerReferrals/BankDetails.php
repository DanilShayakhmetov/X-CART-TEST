<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-bank_details
 *
 * @property string                                                           nick_name
 * @property string                                                           account_number
 * @property string                                                           account_type
 * @property string                                                           currency_code
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Identifier[] identifiers
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Address      branch_location
 * @property bool                                                             mandate_agreed
 */
class BankDetails extends PayPalModel
{
    /**
     * @return string
     */
    public function getNickName()
    {
        return $this->nick_name;
    }

    /**
     * @param string $nick_name
     *
     * @return BankDetails
     */
    public function setNickName($nick_name)
    {
        $this->nick_name = $nick_name;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->account_number;
    }

    /**
     * @param string $account_number
     *
     * @return BankDetails
     */
    public function setAccountNumber($account_number)
    {
        $this->account_number = $account_number;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountType()
    {
        return $this->account_type;
    }

    /**
     * @param string $account_type
     *
     * @return BankDetails
     */
    public function setAccountType($account_type)
    {
        $this->account_type = $account_type;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currency_code;
    }

    /**
     * @param string $currency_code
     *
     * @return BankDetails
     */
    public function setCurrencyCode($currency_code)
    {
        $this->currency_code = $currency_code;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\FinInstIdentifier[]
     */
    public function getIdentifiers()
    {
        return $this->identifiers;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\FinInstIdentifier[] $identifiers
     *
     * @return BankDetails
     */
    public function setIdentifiers($identifiers)
    {
        $this->identifiers = $identifiers;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\FinInstIdentifier $identifier
     *
     * @return BankDetails
     */
    public function addIdentifier($identifier)
    {
        if (!$this->getIdentifiers()) {

            return $this->setIdentifiers([$identifier]);
        }

        return $this->setIdentifiers(
            array_merge($this->getIdentifiers(), [$identifier])
        );
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\FinInstIdentifier $identifier
     *
     * @return BankDetails
     */
    public function removeIdentifier($identifier)
    {
        return $this->setIdentifiers(
            array_diff($this->getIdentifiers(), [$identifier])
        );
    }

    /**
     * @return \PayPal\Api\Address
     */
    public function getBranchLocation()
    {
        return $this->branch_location;
    }

    /**
     * @param \PayPal\Api\Address $branch_location
     *
     * @return BankDetails
     */
    public function setBranchLocation($branch_location)
    {
        $this->branch_location = $branch_location;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMandateAgreed()
    {
        return $this->mandate_agreed;
    }

    /**
     * @param bool $mandate_agreed
     *
     * @return BankDetails
     */
    public function setMandateAgreed($mandate_agreed)
    {
        $this->mandate_agreed = $mandate_agreed;

        return $this;
    }
}
