<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Common;


use XLite\Core\Mail\AMail;
use XLite\Core\Mailer;

class DeletedAdmin extends AMail
{
    public static function getInterface()
    {
        return \XLite::ADMIN_INTERFACE;
    }

    public static function getDir()
    {
        return 'profile_deleted';
    }

    protected static function defineVariables()
    {
        return parent::defineVariables() + [
                'deleted_profile' => 'recipient@example.com',
            ];
    }

    public function __construct($login)
    {
        parent::__construct();
        $this->populateVariables([
            'deleted_profile' => $login,
        ]);
        $this->appendData([
            'deletedLogin' => $login,
        ]);
        $this->setFrom(Mailer::getSiteAdministratorMail());
        $this->setReplyTo($login);
        $this->setTo(Mailer::getUsersDepartmentMails());
    }
}