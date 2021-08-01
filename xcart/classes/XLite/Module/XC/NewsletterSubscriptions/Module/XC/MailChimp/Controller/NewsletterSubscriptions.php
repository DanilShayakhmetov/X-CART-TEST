<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NewsletterSubscriptions\Module\XC\MailChimp\Controller;

/**
 * NewsletterSubscriptions controller
 *
 * @Decorator\Depend ("XC\MailChimp")
 */
class NewsletterSubscriptions extends \XLite\Module\XC\NewsletterSubscriptions\Controller\Customer\NewsletterSubscriptions implements \XLite\Base\IDecorator
{
    /**
     * Subscribe action handler
     */
    protected function doActionSubscribe()
    {
        if ($this->isMailChimpConfigured()) {
            $this->doSubscribeToMailChimp();
        } else {
            parent::doActionSubscribe();
        }
    }

    /**
     * Check if MailChimp module is configured and have lists
     *
     * @return boolean
     */
    protected function isMailChimpConfigured()
    {
        return \XLite\Module\XC\MailChimp\Main::isMailChimpConfigured();
    }

    /**
     * Subscribe to mailchimp
     */
    protected function doSubscribeToMailChimp()
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();

        $email = \XLite\Core\Request::getInstance()->newlettersubscription_email;
        $tempProfile = false;

        if (!$profile || $profile->getLogin() !== $email) {
            $profile = $this->getNewProfileToSubscribe($email);
            $profile->create();
            \XLite\Core\Database::getEM()->persist($profile);
            $tempProfile = true;
        }

        \XLite\Module\XC\MailChimp\Core\MailChimp::processSubscriptionAll(
            $profile
        );

        if ($tempProfile) {
            $profile->delete();   
        }
    }

    /**
     * @param $email
     *
     * @return \XLite\Model\Profile
     */
    protected function getNewProfileToSubscribe($email)
    {
        $profileToSubscribe = new \XLite\Model\Profile();
        $profileToSubscribe->setLogin($email);
        $profileToSubscribe->setAnonymous(true);
        
        return $profileToSubscribe;
    }
}
