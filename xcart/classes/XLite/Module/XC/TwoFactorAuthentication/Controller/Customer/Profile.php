<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\Controller\Customer;

/**
 * Profile management controller
 */
class Profile extends \XLite\Controller\Customer\Profile implements \XLite\Base\IDecorator
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
     * Register profile action
     *
     * @return boolean
     */
    protected function doActionRegister()
    {
        $result = parent::doActionRegister();

        if ($result && $this->getModelForm()) {
            $profile  = $this->getModelForm()->getModelObject();
            $authy_id = $this->getApprovedAuthyIdByPhoneCode($profile->getAuthPhoneCode(), $profile->getAuthPhoneNumber());
            if ($profile->getAuth_2FaEnabled() && $authy_id) {
                $profile->setAuthyId($authy_id);
            } else {
                $profile->setAuth_2FaEnabled(0);
                $profile->setAuthPhoneNumber('');
            }

            \XLite\Core\Database::getEM()->flush();
        }

        return $result;
    }

    /**
     * Modify profile action
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        if (
            !isset(\XLite\Core\Request::getInstance()->auth_2fa_enabled)
            || !\XLite\Core\Config::getInstance()->XC->TwoFactorAuthentication->api_key
        ) {
            // another profile form wo our fields
            parent::doActionUpdate();

            return;
        }

        if (\XLite\Core\Request::getInstance()->mode != 'register') {
            $oldNumber = $this->getProfile()->getAuthPhoneNumber() . $this->getProfile()->getAuthPhoneCode();
            $newNumber = preg_replace('/[^0-9]/', '', \XLite\Core\Request::getInstance()->auth_phone_number)
                . \XLite\Core\Request::getInstance()->auth_phone_code;

            if ($oldNumber != $newNumber) {
                $authy_id = $this->getApprovedAuthyIdByPhoneCode(\XLite\Core\Request::getInstance()->auth_phone_code, \XLite\Core\Request::getInstance()->auth_phone_number);
                if (
                    $authy_id
                    && \XLite\Core\Request::getInstance()->auth_2fa_enabled
                ) {
                    // Assign new AuthyId.
                    $this->getProfile()->setAuthyId($authy_id);
                    $user_is_changing_phone_number = true;
                } else {
                    \XLite\Core\Database::getRepo('XLite\Model\Profile')
                        ->clearAuthyIdById($this->getProfile()->getProfileId());
                }
            }
        }

        $old_2fa_state = $this->getProfile()->getAuth_2FaEnabled();

        parent::doActionUpdate();

        if (
            !\XLite\Core\Request::getInstance()->auth_2fa_enabled
            && $old_2fa_state
            && $this->getModelForm()->isValid()
        ) {
            $this->getProfile()->setAuthPhoneNumber('');
            \XLite\Core\Database::getRepo('XLite\Model\Profile')
                ->clearAuthyIdById($this->getProfile()->getProfileId());
            \XLite\Core\Database::getEM()->flush();
        }
    }
}
