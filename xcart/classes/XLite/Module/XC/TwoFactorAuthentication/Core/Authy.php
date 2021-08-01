<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\Core;

/**
 * Authy client
 */
class Authy extends \XLite\Base\Singleton
{
    /**
     * URL for test mode
     */
    const SANDBOX_URL = 'http://sandbox-api.authy.com';

    /**
     * authy api object
     *
     * @var \Authy_Api
     */
    protected $authyApi;

    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct()
    {
        require_once LC_DIR_MODULES . 'XC' . LC_DS . 'TwoFactorAuthentication' . LC_DS . 'lib' . LC_DS . 'Api.php';
        require_once LC_DIR_MODULES . 'XC' . LC_DS . 'TwoFactorAuthentication' . LC_DS . 'lib' . LC_DS . 'Response.php';
        require_once LC_DIR_MODULES . 'XC' . LC_DS . 'TwoFactorAuthentication' . LC_DS . 'lib' . LC_DS . 'User.php';
        require_once LC_DIR_MODULES . 'XC' . LC_DS . 'TwoFactorAuthentication' . LC_DS . 'lib' . LC_DS . 'Resty.php';

        $config = \XLite\Core\Config::getInstance()->XC->TwoFactorAuthentication;

        if ($config->api_key) {
            $this->authyApi = \XLite\Core\Config::getInstance()->XC->TwoFactorAuthentication->production_mode
                ? new \Authy_Api($config->api_key)
                : new \Authy_Api($config->api_key, static::SANDBOX_URL);
        }
    }

    /**
     * Get Authy_Api object
     *
     * @return \Authy_Api
     */
    public function getAuthyApi()
    {
        return $this->authyApi;
    }

    /**
     * Get authy id from session user
     *
     * @return integer
     */
    public function getAuthyIdFromSession()
    {
        $authyId = null;

        $profileId = \XLite\Core\Session::getInstance()->preauth_authy_profile_id;
        $profile   = \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($profileId);
        if (isset($profile)) {
            $authyId = $profile->getAuthyId();
        }

        return $authyId;
    }

    /**
     * Register Authy user by User from session and return Authy id
     *
     * @return integer
     */
    public function registerAuthyForSessionProfile()
    {
        $profileId = \XLite\Core\Session::getInstance()->preauth_authy_profile_id;
        $profile   = \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($profileId);

        $authyPhone     = preg_replace('/[^0-9]/', '', $profile->getAuthPhoneNumber());
        $authyPhoneCode = preg_replace('/[^0-9]/', '', $profile->getAuthPhoneCode());

        if (empty($authyPhone)) {
            return null;
        }

        $emulated_res = \XLite::getInstance()->getOptions(['two_factor_authentication']) ?: [];
        if (isset($emulated_res['1_register_user_response']) && is_numeric($emulated_res['1_register_user_response'])) {
            // response in emulated mode
            return intval($emulated_res['1_register_user_response']) ? substr($authyPhone, 1) : 0;
        }

        $authyApi  = $this->getAuthyApi();
        $authyUser = $authyApi->registerUser($profile->getLogin(), $authyPhone, $authyPhoneCode);
        $authyId   = null;
        if ($authyUser->ok()) {
            $authyId = $authyUser->id();
            $profile->setAuthyId($authyId);

        } else {
            $label = 'Authy:' . $this->getResponseError($authyUser);
            \XLite\Core\TopMessage::addError($label);
            \XLite\Logger::logCustom(
                'AuthyLogin_error',
                ['register_user_error' => $this->getResponseError($authyUser)]
            );
        }

        return $authyId;
    }

    /**
     * Register Authy user from params
     *
     * @return integer
     */
    public function registerAuthyForParams($email, $code, $phone)
    {
        $authyPhone     = preg_replace('/[^0-9]/', '', $phone);
        $authyPhoneCode = preg_replace('/[^0-9]/', '', $code);

        if (empty($authyPhone)) {
            return null;
        }

        $emulated_res = \XLite::getInstance()->getOptions(['two_factor_authentication']) ?: [];
        if (isset($emulated_res['1_register_user_response']) && is_numeric($emulated_res['1_register_user_response'])) {
            // response in emulated mode
            return intval($emulated_res['1_register_user_response']) ? substr($authyPhone, 1) : 0;
        }

        $authyApi  = $this->getAuthyApi();
        $authyUser = $authyApi->registerUser($email, $authyPhone, $authyPhoneCode);
        $authyId   = null;
        if ($authyUser->ok()) {
            $authyId = $authyUser->id();

        } else {
            $label = 'Authy:' . $this->getResponseError($authyUser);
            \XLite\Core\TopMessage::addError($label);
            \XLite\Logger::logCustom(
                'AuthyLogin_error',
                ['register_user_error' => $this->getResponseError($authyUser)]
            );
        }

        return $authyId;
    }

    /**
     * Send sms to profile from session or by param
     *
     * @return \Authy_Response
     */
    public function sendSMS($in_authyId = null)
    {
        $emulated_res = \XLite::getInstance()->getOptions(['two_factor_authentication']) ?: [];
        if (isset($emulated_res['2_send_sms_response']) && is_numeric($emulated_res['2_send_sms_response'])) {
            $emul_tmp_sms = new \Authy_Response(['status' => intval($emulated_res['2_send_sms_response']) ? 200 : 500, 'body' => 'emulated SendSms error']);

            // response in emulated mode
            return $emul_tmp_sms;
        }

        $authyApi = $this->getAuthyApi();
        $sms      = $authyApi->requestSms($in_authyId ? $in_authyId : $this->getAuthyIdFromSession());
        if (!$sms->ok()) {
            \XLite\Logger::logCustom('AuthyLogin_error', ['send_sms_error' => $this->getResponseError($sms)]);
        }

        return $sms;
    }

    /**
     * Get response error if it is
     *
     * @param \Authy_Response $response Target response
     *
     * @return string
     */
    public function getResponseError(\Authy_Response $response)
    {
        return !is_null($response->errors()->message)
            ? $response->errors()->message
            : null;
    }

    /**
     * Verify SMS token
     *
     * @param string $smsToken SMS token
     *
     * @return boolean
     */
    public function verifyToken($smsToken, $in_authyId = null)
    {
        $emulated_res = \XLite::getInstance()->getOptions(['two_factor_authentication']) ?: [];
        if (isset($emulated_res['3_verify_token_response']) && is_numeric($emulated_res['3_verify_token_response'])) {
            // response in emulated mode
            return (bool) intval($emulated_res['3_verify_token_response']);
        }

        $authyApi     = $this->getAuthyApi();
        $verification = $authyApi->verifyToken($in_authyId ? $in_authyId : $this->getAuthyIdFromSession(), $smsToken);

        return $verification->ok();
    }
}
