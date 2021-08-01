<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Common;


use XLite\Core\Mailer;

class UpgradeSafeMode extends \XLite\Core\Mail\AMail
{
    static function getInterface()
    {
        return \XLite::ADMIN_INTERFACE;
    }

    static function getDir()
    {
        return 'upgrade_access_keys';
    }

    public function __construct()
    {
        parent::__construct();

        $this->setFrom(Mailer::getSiteAdministratorMail());
        $this->setTo(Mailer::getSiteAdministratorMails());
    }
}