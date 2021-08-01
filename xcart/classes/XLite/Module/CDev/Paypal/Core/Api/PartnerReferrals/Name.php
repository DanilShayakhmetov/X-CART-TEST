<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-name
 *
 * @property string prefix
 * @property string given_name
 * @property string surname
 * @property string middle_name
 * @property string suffix
 * @property string alternate_full_name
 */
class Name extends PayPalModel
{
    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     *
     * @return Name
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return string
     */
    public function getGivenName()
    {
        return $this->given_name;
    }

    /**
     * @param string $given_name
     *
     * @return Name
     */
    public function setGivenName($given_name)
    {
        $this->given_name = $given_name;

        return $this;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     *
     * @return Name
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middle_name;
    }

    /**
     * @param string $middle_name
     *
     * @return Name
     */
    public function setMiddleName($middle_name)
    {
        $this->middle_name = $middle_name;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     *
     * @return Name
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlternateFullName()
    {
        return $this->alternate_full_name;
    }

    /**
     * @param string $alternate_full_name
     *
     * @return Name
     */
    public function setAlternateFullName($alternate_full_name)
    {
        $this->alternate_full_name = $alternate_full_name;

        return $this;
    }
}
