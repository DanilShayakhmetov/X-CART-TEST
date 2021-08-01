<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SocialLogin\Controller\Customer;

/**
 * Authorization grants are routed to this controller
 */
class Login extends \XLite\Controller\Customer\Login implements \XLite\Base\IDecorator
{
    /**
     * Add top message if log in is failed
     *
     * @param mixed $result Result of log in procedure
     *
     * @return void
     */
    protected function addLoginFailedMessage($result)
    {
        /** @see \XLite\Module\CDev\SocialLogin\Core\Auth::RESULT_SOCIAL_LOGIN */
        if ($result === \XLite\Core\Auth::RESULT_SOCIAL_LOGIN) {
            \XLite\Core\Event::invalidForm(
                'login-form',
                static::t(
                    'The email you tried to use is already registered in our store. Please try logging in using your X account.',
                    ['provider' => ucfirst($this->foundProfile->getSocialLoginProvider())]
                )
            );
        } else {
            parent::addLoginFailedMessage($result);
        }
    }
}
