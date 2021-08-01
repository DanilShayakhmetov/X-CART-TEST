<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\Controller\Admin;

/**
 * Authy login
 */
class AuthyLogin extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['resend_token', 'requestVerificationSms', 'verifyToken']);
    }

    /**
     * Is redirect needed
     *
     * @return boolean
     */
    public function isRedirectNeeded()
    {
        $result = parent::isRedirectNeeded();

        if ('authy_login' == $this->getTarget()) {
            $result = false;
        }

        return $result;
    }

    /**
     * Check - is current place public or not
     *
     * @return boolean
     */
    protected function isPublicZone()
    {
        return true;
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        if (!isset(\XLite\Core\Session::getInstance()->preauth_authy_profile_id)) {
            $returnURL = $this->buildURL('login');
            $this->setReturnURL($returnURL);
            $this->redirect();
        }

        $authyCore = \XLite\Module\XC\TwoFactorAuthentication\Core\Authy::getInstance();

        if (!$authyCore->getAuthyIdFromSession()) {
            $registeredAuthyId = $authyCore->registerAuthyForSessionProfile();
            if (empty($registeredAuthyId)) {
                $this->loginSessionProfile();
                $this->redirect();
            }

            \XLite\Core\Database::getEM()->flush();
        }

        $sms = $authyCore->sendSMS();

        if (!$sms->ok()) {
            $label = 'Authy:' . $authyCore->getResponseError($sms);
            \XLite\Core\TopMessage::addError($label);
        }
    }

    /**
     * Login action
     *
     * @return void
     */
    protected function doActionLogin()
    {
        $token = \XLite\Core\Request::getInstance()->sms_token;

        $authyCore    = \XLite\Module\XC\TwoFactorAuthentication\Core\Authy::getInstance();
        $verifyResult = $authyCore->verifyToken($token);
        if ($verifyResult) {
            $this->loginSessionProfile();
            $this->doRedirect();
        } else {
            \XLite\Core\TopMessage::addError(static::t('SMS code is invalid. Resend SMS code'));
        }
    }

    /**
     * Step 1. Send sms (if needed) for registration form.
     *
     * @return void
     */
    protected function doActionRequestVerificationSms()
    {
        $email       = \XLite\Core\Request::getInstance()->email;
        $phoneCode   = trim(\XLite\Core\Request::getInstance()->phoneCode, '+ ') ?: '';
        $phoneNumber = preg_replace('/\s/', '', \XLite\Core\Request::getInstance()->phoneNumber) ?: '';

        $current_session = \XLite\Core\Session::getInstance()->two_fa_success_phones ?? [];
        $_key            = $phoneCode . '|' . $phoneNumber;

        if (
            \XLite\Core\Auth::getInstance()->isLogged()
            && ($profile = \XLite\Core\Auth::getInstance()->getProfile())
            && $profile->getAuthPhoneCode() == ('+' . $phoneCode)
            && $profile->getAuthPhoneNumber() == $phoneNumber
            && $profile->getAuthyId()
        ) {
            $res = 's_verification_is_passed';
        } elseif (
            isset($current_session[$_key])
            && isset($current_session[$_key]['emails'][$email])
        ) {
            $res = $current_session[$_key]['result'];
        } else {
            // real api request here

            $authyCore = \XLite\Module\XC\TwoFactorAuthentication\Core\Authy::getInstance();
            // First request to register a new user or return the existent one
            $authy_id = $authyCore->registerAuthyForParams($email, $phoneCode, $phoneNumber) ?: false;

            if ($authy_id) {
                // Second request to send SMS
                $sms = $authyCore->sendSMS($authy_id);

                static::log(['send_token_from_profile' => $sms]);

                if (!$sms->ok()) {
                    $label = 'Authy:' . $authyCore->getResponseError($sms);
                    \XLite\Core\TopMessage::addError($label);
                    $res                    = '2invalid_cannot_send_sms';
                    $current_session[$_key] = null;
                } else {
                    // Save additional email for the same AuthyId
                    $emails                 = isset($current_session[$_key]['emails']) ? $current_session[$_key]['emails'] : [];
                    $emails[$email]         = 1;
                    $res                    = 's_verification_is_needed';
                    $current_session[$_key] = ['result' => $res, 'authyid' => $authy_id, 'emails' => $emails];
                }
            } else {
                $res                    = '1invalid_cannot_register_user_a';
                $current_session[$_key] = null;
            }

            \XLite\Core\Session::getInstance()->two_fa_success_phones = $current_session;
        }

        $this->setSuppressOutput(true);
        $this->silent = true;
        $this->displayJSON($res);
    }

    /**
     * Step 2. Verify sms for registration form.
     *
     * @return void
     */
    protected function doActionVerifyToken()
    {
        $filled_sms = \XLite\Core\Request::getInstance()->smsCode;

        $phoneCode   = trim(\XLite\Core\Request::getInstance()->phoneCode, '+ ') ?: '';
        $phoneNumber = preg_replace('/\s/', '', \XLite\Core\Request::getInstance()->phoneNumber) ?: '';

        $current_session = \XLite\Core\Session::getInstance()->two_fa_success_phones ?? [];
        $_key            = $phoneCode . '|' . $phoneNumber;
        if (!empty($current_session[$_key]['authyid'])) {
            if ($current_session[$_key]['result'] == 's_verification_is_passed') {
                $verifyResult = 's_verification_is_passed';
            }
        } else {
            // The Step 1(non-empty session) is required
            $verifyResult = '2invalid_cannot_register_user_b';
        }

        if (!isset($verifyResult)) {
            // real api request here
            $authyCore    = \XLite\Module\XC\TwoFactorAuthentication\Core\Authy::getInstance();
            $verifyResult = $authyCore->verifyToken($filled_sms, $current_session[$_key]['authyid']);

            if ($verifyResult) {
                $verifyResult                     = 's_verification_is_passed';
                $current_session[$_key]['result'] = $verifyResult;
            } else {
                $verifyResult = '3invalid_sms_typed';
            }
            \XLite\Core\Session::getInstance()->two_fa_success_phones = $current_session;
        }

        $this->setSuppressOutput(true);
        $this->silent = true;
        $this->displayJSON($verifyResult);
    }

    /**
     * Resend sms token action
     *
     * @return void
     */
    protected function doActionResendToken()
    {
        $this->setSilenceClose(true);

        $authyCore = \XLite\Module\XC\TwoFactorAuthentication\Core\Authy::getInstance();
        $sms       = $authyCore->sendSMS();

        if (!$sms->ok()) {
            $label = 'Authy:' . $authyCore->getResponseError($sms);
            \XLite\Core\TopMessage::addError($label);
        }
    }

    /**
     * Login session profile
     *
     * @return void
     */
    protected function loginSessionProfile()
    {
        $profileId = \XLite\Core\Session::getInstance()->preauth_authy_profile_id;
        $profile   = \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($profileId);
        if (isset($profile)) {
            \XLite\Core\Auth::getInstance()->loginProfile($profile);
        }

        $returnURL = $this->buildURL();
        $this->setReturnURL($returnURL);
    }

    /**
     * Logging the data under AuthyLogin
     * Available if developer_mode is on in the config file
     *
     * @param mixed $data Log data
     *
     * @return void
     */
    protected static function log($data)
    {
        if (LC_DEVELOPER_MODE) {
            \XLite\Logger::logCustom('AuthyLogin', $data);
        }
    }
}
