<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\View\Model\Profile;

use XLite\Core\Auth;
use XLite\Core\Config;

/**
 * Administrator profile model widget. This widget is used in the admin interface
 */
class AdminMain extends \XLite\View\Model\Profile\AdminMain implements \XLite\Base\IDecorator
{
    /**
     * Schema for phone number and phone country code
     *
     * @var array
     */
    protected $auth_phone = [
        'auth_2fa_enabled'      => [
            self::SCHEMA_CLASS      => '\XLite\Module\XC\TwoFactorAuthentication\View\FormField\Input\Checkbox',
            self::SCHEMA_LABEL      => 'Use Two-Factor Authentication',
            self::SCHEMA_REQUIRED   => false,
            self::SCHEMA_IS_CHECKED => false,
        ],
        'auth_phone_code'       => [
            self::SCHEMA_CLASS       => '\XLite\Module\XC\TwoFactorAuthentication\View\FormField\Select\PhoneCode',
            self::SCHEMA_LABEL       => 'Country phone code',
            self::SCHEMA_REQUIRED    => false,
            self::SCHEMA_PLACEHOLDER => '+1',
            self::SCHEMA_DEPENDENCY  => [
                self::DEPENDENCY_SHOW => [
                    'auth_2fa_enabled' => [1],
                ],
            ],

        ],
        'auth_phone_number'     => [
            self::SCHEMA_CLASS      => '\XLite\Module\XC\TwoFactorAuthentication\View\FormField\Input\Text\Phone',
            self::SCHEMA_LABEL      => 'Phone number',
            self::SCHEMA_REQUIRED   => true,
            self::SCHEMA_HELP       => 'Enter your phone number here to receive an SMS code for two-factor authentication',
            self::SCHEMA_DEPENDENCY => [
                self::DEPENDENCY_SHOW => [
                    'auth_2fa_enabled' => [1],
                ],
            ],

        ],
        'auth_confirm_password' => [
            self::SCHEMA_CLASS            => 'XLite\View\FormField\Input\Password',
            self::SCHEMA_LABEL            => 'Enter your current password',
            self::SCHEMA_REQUIRED         => true,
            self::SCHEMA_HELP             => 'Enter your current password to confirm the operation',
            self::SCHEMA_MODEL_ATTRIBUTES => [
                \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
            ],
        ],
        'auth_sms_code'         => [
            self::SCHEMA_CLASS      => '\XLite\Module\XC\TwoFactorAuthentication\View\FormField\Input\Text\SMSCode',
            self::SCHEMA_LABEL      => 'SMS code',
            self::SCHEMA_REQUIRED   => false,
            self::SCHEMA_HELP       => 'Enter your SMS code for two-factor authentication',
            self::SCHEMA_DEPENDENCY => [
                self::DEPENDENCY_SHOW => [
                    'auth_2fa_enabled' => [1],
                ],
            ],

        ],
    ];

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionMain()
    {
        if (!Config::getInstance()->XC->TwoFactorAuthentication->api_key) {
            foreach ($this->auth_phone as $k => $field) {
                $this->auth_phone[$k][self::SCHEMA_ATTRIBUTES] = ['readonly' => true];
            }
        }

        if (
            Config::getInstance()->XC->TwoFactorAuthentication->admin_interface
            || Config::getInstance()->XC->TwoFactorAuthentication->customer_interface
        ) {
            $session_profile = \XLite\Core\Auth::getInstance()->getProfile();

            if (
                $this->isRegisterMode()
                || (
                    !empty($session_profile)
                    && !$session_profile->getAuth_2FaEnabled() // 2fa is disabled for the current profile
                )
                || (
                    !empty($session_profile)
                    && $session_profile->getProfileId() != $this->getModelObject()->getProfileId()
                )
            ) {
                // additional password isn't needed for: new users / for users wo auth_2fa_enabled / while modifying another profile
                unset($this->auth_phone['auth_confirm_password']);

                if (
                    !empty($session_profile)
                    && $session_profile->getProfileId() != $this->getModelObject()->getProfileId()
                    && Auth::getInstance()->isAdmin($session_profile)
                ) {
                    // the current admin is modifying another profile, disable all tests
                    unset($this->auth_phone['auth_sms_code']);
                }
            }
            $this->mainSchema = array_merge($this->mainSchema, $this->auth_phone);
        }

        return parent::getFormFieldsForSectionMain();
    }

    public function getDefaultFieldValue($name)
    {
        static $def_auth_phone_code = null;

        $res_parent = parent::getDefaultFieldValue($name);

        if ('auth_phone_code' == $name && is_null($res_parent)) {
            if (!isset($def_auth_phone_code)) {
                $def_country_code    = (\XLite\Model\Address::getDefaultFieldValue('country') ? \XLite\Model\Address::getDefaultFieldValue('country')->getCode() : 'US');
                $all_codes           = \XLite\Module\XC\TwoFactorAuthentication\Core\PhoneCountryCodes::getInstance()->getList();
                $def_auth_phone_code = !empty($all_codes[$def_country_code]) ? $all_codes[$def_country_code] : '';
            }
            $res_parent = $def_auth_phone_code;
        }

        return $res_parent;
    }

    /**
     * @param \XLite\View\FormField\AFormField $field Form field object
     * @param array                            $formFields
     *
     * @return array
     */
    protected function validateFormFieldAuthConfirmPasswordValue($field, $formFields)
    {
        $_login    = $this->getModelObject()->getLogin();
        $_password = $field->getValue();

        [$profile, $result] = \XLite\Core\Auth::getInstance()->checkLoginPassword($_login, $_password);
        if (!isset($profile) || $result !== true) {
            $errorMessage = static::t(
                'The password entered does not match the password stored for this user name',
                ['value' => $_password]
            );

            [$profile, $result] = \XLite\Core\Auth::getInstance()->checkLoginPassword($_login, $_password);
        }

        return [empty($errorMessage), $errorMessage];
    }

}
