<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request\Audience;

use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;

class UnSubscribe extends MailChimpRequest
{
    /**
     * @param string $listId
     * @param string $hash
     */
    public function __construct($listId, $hash)
    {
        parent::__construct('UnSubscribe from lists (audience)', 'delete', "lists/{$listId}/members/{$hash}");
    }

    /**
     * @param string $listId
     * @param string $hash
     *
     * @return self
     */
    public static function getRequest($listId, $hash): self
    {
        return new self($listId, $hash);
    }

    /**
     * @param string $listId
     * @param string $hash
     *
     * @return mixed
     */
    public static function executeAction($listId, $hash)
    {
        return self::getRequest($listId, $hash)->execute();
    }
}
