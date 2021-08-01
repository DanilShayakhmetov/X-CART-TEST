<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Common;


use XLite\Core\Converter;
use XLite\Core\Mailer;
use XLite\Model\AccessControlCell;
use XLite\Model\Profile;

class AccessLinkCustomer extends \XLite\Core\Mail\AMail
{
    static function getInterface()
    {
        return \XLite::CUSTOMER_INTERFACE;
    }

    static function getDir()
    {
        return 'access_link';
    }

    protected static function defineVariables()
    {
        return [
                'first_name' => static::t('Joe'),
            ] + parent::defineVariables();
    }

    public function __construct(Profile $profile, AccessControlCell $acc)
    {
        parent::__construct();

        $this->setFrom(Mailer::getSiteAdministratorMail());
        $this->setTo(['email' => $profile->getLogin(), 'name' => $profile->getName(false)]);
        $this->setReplyTo(Mailer::getSiteAdministratorMails());
        $this->tryToSetLanguageCode($profile->getLanguage());

        $returnData = $acc->getReturnData();

        $link = Converter::buildPersistentAccessURL(
            $acc,
            isset($returnData['target']) ? $returnData['target'] : '',
            isset($returnData['action']) ? $returnData['action'] : '',
            isset($returnData['params']) ? $returnData['params'] : [],
            \XLite::getCustomerScript()
        );

        $this->appendData([
            'access_link' => $link,
            'profile' => $profile,
            'recipient_name' => $profile->getName(),
        ]);
        $this->populateVariables([
            'first_name' => $profile->getName(true, true),
        ]);
    }
}