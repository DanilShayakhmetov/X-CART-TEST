<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request\Audience;

use XLite\Core\Request;
use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;

class GetAll extends MailChimpRequest
{
    public function __construct()
    {
        parent::__construct('Getting lists (audience)', 'get', "lists");
    }

    /**
     * @return self
     */
    public static function getRequest(): self
    {
        return new self();
    }

    /**
     * @return mixed
     */
    public static function executeAction()
    {
        $result = self::getRequest()->execute();

        return $result['lists'] ?? null;
    }
}
