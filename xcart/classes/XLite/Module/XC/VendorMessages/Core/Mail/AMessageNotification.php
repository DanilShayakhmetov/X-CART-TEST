<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Core\Mail;


use XLite\Core\Config;
use XLite\Core\Mailer;
use XLite\Model\Profile;
use XLite\Module\XC\VendorMessages\Model\Message;

abstract class AMessageNotification extends \XLite\Core\Mail\AMail
{
    static function getInterface()
    {
        return \XLite::CUSTOMER_INTERFACE;
    }

    protected static function defineVariables()
    {
        return array_merge(parent::defineVariables(), [
            'message'           => '',
            'conversation_link' => sprintf(
                '<a href="%s">%s</a>',
                \XLite::getInstance()->getShopURL(),
                static::t('Conversation: X', ['members' => 'recipient@example.com'])
            ),
        ]);
    }

    public function __construct(Message $message, Profile $recipient = null)
    {
        parent::__construct();

        $this->setFrom(Mailer::getOrdersDepartmentMail());
        $this->setTo($recipient ? $recipient->getLogin() : Mailer::getOrdersDepartmentMail());
        $this->tryToSetLanguageCode(
            $recipient
                ? $recipient->getLanguage()
                : Config::getInstance()->General->default_admin_language
        );

        $url = \XLite\Core\Converter::buildURL(
            'conversation',
            null,
            ['id' => $message->getConversation()->getId()],
            $recipient && !$recipient->isAdmin()
                ? \XLite::getCustomerScript()
                : \XLite::getAdminScript()
        );

        $this->populateVariables([
            'first_name'        => $recipient ? $recipient->getName(true, true) : static::t('na_admin'),
            'message'           => $message->getPublicBody(),
            'conversation_link' => sprintf(
                '<a href="%s">%s</a>',
                htmlentities(\XLite::getInstance()->getShopURL($url)),
                $message->getConversation()->getName($recipient)
            ),
        ]);

        $this->appendData([
            'message'    => $message,
            'targetType' => $recipient && !$recipient->isAdmin()
                ? Mailer::RECIPIENT_CUSTOMER
                : Mailer::RECIPIENT_ADMIN,
        ]);

        if ($recipient) {
            $this->populateVariables(['recipient_name' => $recipient->getNameForMessages()]);
        } else {
            $this->populateVariables(['recipient_name' => Config::getInstance()->Company->company_name]);
        }
    }

    /**
     * @return Message|null
     */
    protected function getMessage()
    {
        return $this->getData()['message'];
    }
}