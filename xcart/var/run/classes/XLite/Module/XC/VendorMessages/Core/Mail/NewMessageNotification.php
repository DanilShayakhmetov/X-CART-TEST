<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Core\Mail;


use XLite\Core\Config;
use XLite\Model\Profile;
use XLite\Module\XC\VendorMessages\Model\Message;

class NewMessageNotification extends AMessageNotification
{
    static function getDir()
    {
        return 'modules/XC/VendorMessages/new_message_notification';
    }

    public function __construct(Message $message, Profile $recipient = null)
    {
        parent::__construct($message, $recipient);

        if ($recipient) {
            $this->appendData(['recipient' => $recipient]);
        } else {
            $this->appendData(['recipient_name' => Config::getInstance()->Company->company_name]);
        }
    }
}