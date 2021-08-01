<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request\Customer;

use XLite\Core\Request;
use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;

class Get extends MailChimpRequest
{
    /**
     * @return string|null
     */
    public static function getUserIdFromRequest(): ?string
    {
        /** @var \XLite\Core\Request|\XLite\Module\XC\MailChimp\Core\Request $request */
        $request = Request::getInstance();

        return $request->{$request::MAILCHIMP_USER_ID} ?? null;
    }
}
