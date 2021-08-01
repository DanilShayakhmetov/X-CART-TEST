<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\Controller\Admin;

/**
 * Profile management controller
 */
class Profile extends \XLite\Controller\Admin\Profile implements \XLite\Base\IDecorator
{
    /**
     * @return int
     */
    protected function getApprovedAuthyIdByPhoneCode($phone_code, $phone_number)
    {
        $auth_data = \XLite\Core\Session::getInstance()->two_fa_success_phones[trim($phone_code, '+ ') . '|' . preg_replace('/[^0-9]/', '', $phone_number)] ?? [];
        if (
            !empty($auth_data['result'])
            && $auth_data['result'] == 's_verification_is_passed'
            && !empty($auth_data['authyid'])
        ) {
            return $auth_data['authyid'];
        }

        return 0;
    }

    /**
     * Modify profile action
     *
     * @return void
     */
    protected function doActionModify()
    {
        if (
            !isset(\XLite\Core\Request::getInstance()->auth_2fa_enabled)
            || !\XLite\Core\Config::getInstance()->XC->TwoFactorAuthentication->api_key
        ) {
            // another profile form wo our fields
            parent::doActionModify();

            return;
        }

        $profile2modify = $this->getProfile();

        $oldNumber = $profile2modify->getAuthPhoneNumber() . $profile2modify->getAuthPhoneCode();
        $newNumber = preg_replace('/[^0-9]/', '', \XLite\Core\Request::getInstance()->auth_phone_number)
            . \XLite\Core\Request::getInstance()->auth_phone_code;

        $session_profile = \XLite\Core\Auth::getInstance()->getProfile();

        if (
            !empty($session_profile)
            && $session_profile->getProfileId() != $profile2modify->getProfileId()
        ) {
            if (\XLite\Core\Auth::getInstance()->isAdmin($session_profile)) {
                // An admin is modifying another profile, disable all tests
                if (
                    $oldNumber != $newNumber
                    || (
                        !empty($newNumber)
                        && !$profile2modify->getAuthyId() // old is OFF
                        && \XLite\Core\Request::getInstance()->auth_2fa_enabled // the admin is trying to enable 2fa
                    )
                ) {
                    $authyCore    = \XLite\Module\XC\TwoFactorAuthentication\Core\Authy::getInstance();
                    $new_authy_id = $authyCore->registerAuthyForParams(\XLite\Core\Request::getInstance()->login, \XLite\Core\Request::getInstance()->auth_phone_code, preg_replace('/[^0-9]/', '', \XLite\Core\Request::getInstance()->auth_phone_number)) ?: null;
                    $profile2modify->setAuthyId($new_authy_id);
                }
            } else {
                // deny to modify phone number
                \XLite\Core\TopMessage::addError('You cannot modify the phone number');

                return false;
            }

        } else {
            // An user is modifying itself profile
            if ($oldNumber != $newNumber) {
                $authy_id = $this->getApprovedAuthyIdByPhoneCode(\XLite\Core\Request::getInstance()->auth_phone_code, \XLite\Core\Request::getInstance()->auth_phone_number);
                if (
                    $authy_id
                    && \XLite\Core\Request::getInstance()->auth_2fa_enabled
                ) {
                    // Assign new AuthyId.
                    $profile2modify->setAuthyId($authy_id);
                } else {
                    \XLite\Core\Database::getRepo('XLite\Model\Profile')->clearAuthyIdById($profile2modify->getProfileId());
                }
            }
        }

        parent::doActionModify();
    }
}
