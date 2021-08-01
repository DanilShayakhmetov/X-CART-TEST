<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Common;

class ChangeCloudDomain extends \XLite\Core\Mail\AMail
{
    const HELPDESK_EMAIL = 'helpdesk@x-cart.com';

    /**
     * @return string
     */
    static function getInterface()
    {
        return \XLite::ADMIN_INTERFACE;
    }

    /**
     * @return string
     */
    static function getDir()
    {
        return 'change_cloud_domain';
    }

    public function __construct($domainName, $domainAvailable = false)
    {
        parent::__construct();

        $this->setFrom(null);
        $this->setTo(static::HELPDESK_EMAIL);

        $cloudAccountEmail = \XLite::getInstance()->getOptions(['service', 'cloud_account_email']);
        if ($cloudAccountEmail) {
            $this->addReplyTo($cloudAccountEmail);
        }

        $this->appendData([
            'domainName'      => $domainName,
            'domainAvailable' => $domainAvailable,
        ]);
    }

    /**
     * @return bool
     */
    public function isSeparateMailer()
    {
        return true;
    }

    /**
     * @param \XLite\View\Mailer $mailer
     *
     * @return \XLite\View\Mailer
     */
    public function prepareSeparateMailer(\XLite\View\Mailer $mailer)
    {
        $mailer = parent::prepareSeparateMailer($mailer);

        $mailer->setSubjectTemplate('change_cloud_domain/subject.twig');
        $mailer->setLayoutTemplate('change_cloud_domain/body.twig');

        return $mailer;
    }
}