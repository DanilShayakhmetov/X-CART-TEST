<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ContactUs\Module\XC\ThemeTweaker\Core\Notifications;


use XLite\Core\Mailer;
use XLite\Module\CDev\ContactUs\Model\Contact;

/**
 * StaticProvider
 *
 * @Decorator\Depend("XC\ThemeTweaker")
 */
class StaticProvider extends \XLite\Module\XC\ThemeTweaker\Core\Notifications\StaticProvider implements \XLite\Base\IDecorator
{
    protected static function getNotificationsStaticData()
    {
        return parent::getNotificationsStaticData() + [
                'modules/CDev/ContactUs/message' => [
                    'contact' => static::getContactMock(),
                    'emails'  => Mailer::getSiteAdministratorMails(),
                ],
            ];
    }

    /**
     * @return Contact
     */
    protected static function getContactMock()
    {
        $contact = new Contact();
        $contact->setSubject('Test message')
            ->setName('John Doe')
            ->setEmail('email@example.com')
            ->setMessage('Lorem ipsum dolor sit amet, consectetur adipisicing elit. A, ab architecto aut commodi consequatur delectus distinctio earum excepturi iusto laboriosam quaerat recusandae, repellendus ut, veritatis vitae? Ipsum iste nostrum saepe!');

        return $contact;
    }
}