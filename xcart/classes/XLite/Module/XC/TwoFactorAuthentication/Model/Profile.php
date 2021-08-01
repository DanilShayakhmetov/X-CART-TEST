<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\Model;

/**
 * Authy Profile
 */
abstract class Profile extends \XLite\Model\Profile implements \XLite\Base\IDecorator
{
    /**
     * Authentication phone number
     *
     * @var string
     *
     * @Column (type="string", nullable=true)
     */
    protected $auth_phone_number;

    /**
     * Authentication phone country code
     *
     * @var string
     *
     * @Column (type="string", nullable=true)
     */
    protected $auth_phone_code;

    /**
     * Authentication phone is enabled ot not
     *
     * @var boolean
     *
     * @Column (type="boolean", nullable=true, options={"default":"0"})
     */
    protected $auth_2fa_enabled;

    /**
     * ID in Authy
     *
     * @var integer
     *
     * @Column (type="integer", nullable=true)
     */
    protected $authy_id;

    /**
     * @return bool
     */
    public function getAuth_2FaEnabled(): bool
    {
        return (bool) $this->auth_2fa_enabled;
    }

    /**
     * @param $auth_2fa_enabled
     *
     * @return Profile
     */
    public function setAuth_2FaEnabled($auth_2fa_enabled): Profile
    {
        $this->auth_2fa_enabled = (bool) $auth_2fa_enabled;

        return $this;
    }

    /**
     * Get auth_phone_number
     *
     * @return string
     */
    public function getAuthPhoneNumber()
    {
        return $this->auth_phone_number;
    }

    /**
     * Set auth_phone_number
     *
     * @param string $authPhoneNumber
     *
     * @return Profile
     */
    public function setAuthPhoneNumber($authPhoneNumber)
    {
        $this->auth_phone_number = $authPhoneNumber;

        return $this;
    }

    /**
     * Get auth_phone_code
     *
     * @return string
     */
    public function getAuthPhoneCode()
    {
        return $this->auth_phone_code;
    }

    /**
     * Set auth_phone_code
     *
     * @param string $authPhoneCode
     *
     * @return Profile
     */
    public function setAuthPhoneCode($authPhoneCode)
    {
        $this->auth_phone_code = $authPhoneCode;

        return $this;
    }

    /**
     * Get authy_id
     *
     * @return integer
     */
    public function getAuthyId()
    {
        return $this->authy_id;
    }

    /**
     * Set authy_id
     *
     * @param integer $authyId
     *
     * @return Profile
     */
    public function setAuthyId($authyId)
    {
        $this->authy_id = $authyId;

        return $this;
    }
}
