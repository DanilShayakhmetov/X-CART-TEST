<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Common;


use XLite\Core\Converter;
use XLite\Core\Mail\AMail;
use XLite\Core\Mailer;

class FailedLoginAdmin extends AMail
{
    public static function getInterface()
    {
        return \XLite::ADMIN_INTERFACE;
    }

    public static function getDir()
    {
        return 'failed_admin_login';
    }

    protected static function defineVariables()
    {
        return [
                'login'      => 'admin@example.com',
                'ip'         => '192.168.1.1',
                'reset_link' => \XLite::getInstance()->getShopURL(),
            ] + parent::defineVariables();
    }

    public function __construct($login, $ip)
    {
        parent::__construct();

        $this->setFrom(Mailer::getSiteAdministratorMail());
        $this->setReplyTo(Mailer::getSiteAdministratorMails());

        $this->setTo($login);

        $this->populateVariables([
            'login'      => $login,
            'ip'         => $ip,
            'reset_link' => Converter::buildFullURL(
                'recover_password',
                '',
                ['email' => $login],
                \XLite::getAdminScript()
            ),
        ]);

        $this->appendData([
            'login'       => $login,
            'REMOTE_ADDR' => $ip,
        ]);
    }
}