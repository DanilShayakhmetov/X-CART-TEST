<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Profile;


use XLite\Core\Converter;

class RecoverPasswordAdmin extends AProfile
{
    public static function getInterface()
    {
        return \XLite::ADMIN_INTERFACE;
    }

    public static function getDir()
    {
        return 'recover_password_request';
    }

    protected static function defineVariables()
    {
        return [
                'recover_url' => \XLite::getInstance()->getShopURL(),
            ] + parent::defineVariables();
    }

    public function __construct(\XLite\Model\Profile $profile, $resetKey)
    {
        parent::__construct($profile);

        $url = \XLite::getInstance()->getShopURL(
            Converter::buildURL(
                'recover_password',
                'confirm',
                [
                    'email'      => $profile->getLogin(),
                    'request_id' => $resetKey,
                ],
                \XLite::getAdminScript()
            )
        );

        $this->populateVariables([
            'recover_url' => $url,
        ]);

        $this->appendData([
            'url' => $url,
        ]);
        $this->setFrom(\XLite\Core\Mailer::getUsersDepartmentMail());
        $this->setReplyTo(\XLite\Core\Mailer::getUsersDepartmentMails());
        $this->setTo(['email' => $profile->getLogin(), 'name' => $profile->getName(false)]);
    }
}