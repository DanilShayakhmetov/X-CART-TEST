<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\View\Model\Profile;

use XLite\Core\Config;

/**
 * \XLite\View\Model\Profile\Main
 */
class Main extends \XLite\View\Model\Profile\Main implements \XLite\Base\IDecorator
{
    /**
     * Schema for phone number and phone country code
     *
     * @var array
     */
    protected $auth_phone = [
        'auth_2fa_enabled'      => [
            self::SCHEMA_CLASS                                    => '\XLite\Module\XC\TwoFactorAuthentication\View\FormField\Input\Checkbox',
            self::SCHEMA_LABEL                                    => 'Use Two-Factor Authentication',
            self::SCHEMA_REQUIRED                                 => false,
            self::SCHEMA_IS_CHECKED                               => false,
            \XLite\View\FormField\AFormField::PARAM_WRAPPER_CLASS => 'input input-checkbox two-fa-checkbox not-floating',
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
     * @inheritDoc
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'modules/XC/TwoFactorAuthentication/profile/2fa_checkbox.css',
            ]
        );
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

        if (Config::getInstance()->XC->TwoFactorAuthentication->customer_interface) {
            if (
                $this->isRegisterMode()
                || (
                    \XLite\Core\Auth::getInstance()->isLogged()
                    && ($profile = \XLite\Core\Auth::getInstance()->getProfile())
                    && !$profile->getAuth_2FaEnabled()
                )
            ) {
                // additional password isn't needed for: new users and for users wo auth_2fa_enabled
                unset($this->auth_phone['auth_confirm_password']);
            }
            $this->mainSchema = array_merge($this->mainSchema, $this->auth_phone);
        }

        return parent::getFormFieldsForSectionMain();
    }

    /**
     * @param \XLite\View\FormField\AFormField $field Form field object
     * @param array                            $formFields
     *
     * @return array
     */
    protected function validateFormFieldAuthConfirmPasswordValue($field, $formFields)
    {
        $errorMessage = '';
        if (
            \XLite\Core\Request::getInstance()->mode != 'register'
            && $this->getModelObject()->getProfileId()
        ) {
            $profile2modify = \XLite\Core\Database::getRepo('XLite\Model\Profile')->getArrayData($this->getModelObject()->getProfileId());

            $oldNumber = $profile2modify['auth_phone_number'] . $profile2modify['auth_phone_code'];
            $newNumber = preg_replace('/[^0-9]/', '', \XLite\Core\Request::getInstance()->auth_phone_number)
                . \XLite\Core\Request::getInstance()->auth_phone_code;

            if (
                !\XLite\Core\Request::getInstance()->auth_2fa_enabled // the user is trying to disable 2fa
                || $oldNumber != $newNumber // Or the user is trying to change phone number
            ) {
                $_login    = $profile2modify['login'];
                $_password = $field->getValue();

                [$profile, $result] = \XLite\Core\Auth::getInstance()->checkLoginPassword($_login, $_password);
                if (!isset($profile) || $result !== true) {
                    $errorMessage = static::t(
                        'The password entered does not match the password stored for this user name',
                        ['value' => $_password]
                    );

                    [$profile, $result] = \XLite\Core\Auth::getInstance()->checkLoginPassword($_login, $_password);
                }
            }
        }

        return [empty($errorMessage), $errorMessage];
    }

}
