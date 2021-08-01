<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ContactUs\Core;

use XLite\Module\CDev\ContactUs\Core\Mail\ContactUsMessage;

/**
 * Mailer
 */
abstract class Mailer extends \XLite\Core\Mailer implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Module\CDev\ContactUs\Model\Contact  $contact
     * @param string|array $email Email
     *
     * @return string | null
     */
    public static function sendContactUsMessage($contact, $email)
    {
        (new ContactUsMessage($contact, $email))->schedule();
    }
}
