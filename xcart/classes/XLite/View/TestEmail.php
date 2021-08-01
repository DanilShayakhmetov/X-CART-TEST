<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XLite\Core\Config;
use XLite\View\FormField\Select\EmailFrom;

/**
 * \XLite\View\TestEmail
 */
class TestEmail extends \XLite\View\Dialog
{
    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'test_email';
    }

    /**
     * @return string
     */
    protected function getMailTesterArticleLink()
    {
        return 'https://kb.x-cart.com/setting_up_x-cart_5_environment/testing_your_email_transfer_settings_with_mail-tester.html';
    }

    /**
     * @return string
     */
    protected function getCompanyName()
    {
        return Config::getInstance()->Company->company_name;
    }

    /**
     * @return bool
     */
    protected function isContactMode()
    {
        return EmailFrom::OPTION_FROM_CONTACT === Config::getInstance()->Email->mail_from_type;
    }

    /**
     * @return bool
     */
    protected function isServerMode()
    {
        return EmailFrom::OPTION_FROM_SERVER === Config::getInstance()->Email->mail_from_type;
    }

    /**
     * @return bool
     */
    protected function isManualMode()
    {
        return EmailFrom::OPTION_MANUAL === Config::getInstance()->Email->mail_from_type;
    }

    /**
     * @return string
     */
    protected function getManualMail()
    {
        return Config::getInstance()->Email->mail_from_manual;
    }

    /**
     * @return array
     */
    protected function getContactEmailsAsOptions()
    {
        $list = array_unique([
            \XLite\Core\Mailer::getSiteAdministratorMail(false),
            \XLite\Core\Mailer::getUsersDepartmentMail(false),
            \XLite\Core\Mailer::getOrdersDepartmentMail(false),
            \XLite\Core\Mailer::getSupportDepartmentMail(false),
        ]);

        return array_reduce($list, function ($acc, $email) {
            $acc[$email] = $this->getCompanyName() . ' ' . $email;
            return $acc;
        }, []);
    }
}
