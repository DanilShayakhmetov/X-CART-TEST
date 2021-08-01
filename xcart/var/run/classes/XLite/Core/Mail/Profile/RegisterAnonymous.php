<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Profile;


class RegisterAnonymous extends AProfile
{
    public static function getInterface()
    {
        return \XLite::CUSTOMER_INTERFACE;
    }

    public static function getDir()
    {
        return 'register_anonymous';
    }

    protected static function defineVariables()
    {
        return [
                'customer_email'    => static::t('Email'),
                'customer_password' => static::t('Password'),
            ] + parent::defineVariables();
    }

    public function __construct(\XLite\Model\Profile $profile, $password)
    {
        parent::__construct($profile);
        $this->setFrom(\XLite\Core\Mailer::getSiteAdministratorMail());
        $this->setReplyTo(\XLite\Core\Mailer::getSiteAdministratorMails());

        $this->populateVariables([
            'customer_email'    => $profile->getEmail(),
            'customer_password' => $password,
            'recipient_name'    => $profile->getName(),
        ]);

        $this->appendData([
            'password' => $password,
        ]);
    }
}