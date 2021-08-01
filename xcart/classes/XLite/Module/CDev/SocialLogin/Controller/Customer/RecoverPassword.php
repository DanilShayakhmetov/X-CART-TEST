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
class RecoverPassword extends \XLite\Controller\Customer\RecoverPassword implements \XLite\Base\IDecorator
{
    /**
     * Is profile can be recovered
     *
     * @param \XLite\Module\CDev\SocialLogin\Model\Profile|\XLite\Model\Profile $profile
     *
     * @return bool
     */
    protected function isRecoverAllowed(\XLite\Model\Profile $profile)
    {
        return parent::isRecoverAllowed($profile) && !$profile->isSocialProfile();
    }

    /**
     * @param string $email
     */
    protected function requestRecoverPasswordFailed($email)
    {
        /** @var \XLite\Module\CDev\SocialLogin\Model\Profile $profile */
        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin($email);
        if ($profile && $profile->isSocialProfile()) {
            $this->setReturnURL($this->buildURL('recover_password'));

            if (!$this->isAJAX()) {
                \XLite\Core\TopMessage::addError('Sorry, the password reset option is not available for this user account');
            }

            \XLite\Core\Event::invalidElement('email', static::t('Sorry, the password reset option is not available for this user account'));
        } else {
            parent::requestRecoverPasswordFailed($email);
        }
    }
}
