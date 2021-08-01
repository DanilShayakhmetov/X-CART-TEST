<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request\Customer;

use XLite\Core\Cache\ExecuteCached;
use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;

class Check extends MailChimpRequest
{
    /**
     * @param string $storeId
     * @param string $customerId
     */
    public function __construct($storeId, $customerId)
    {
        parent::__construct('Checking customer', 'get', "ecommerce/stores/{$storeId}/customers/{$customerId}", ['fields' => 'id']);
    }

    /**
     * @param string $storeId
     * @param string $customerId
     *
     * @return self
     */
    public static function getRequest($storeId, $customerId): self
    {
        return new self($storeId, $customerId);
    }

    /**
     * @param string $storeId
     * @param string $customerId
     *
     * @return mixed
     */
    public static function executeAction($storeId, $customerId)
    {
        return ExecuteCached::executeCached(
            static function () use ($storeId, $customerId) {
                return self::getRequest($storeId, $customerId)->execute();
            },
            [self::class, $storeId, $customerId]
        );
    }

    /**
     * @param string $storeId
     * @param string $customerId
     */
    public static function dropActionCache($storeId, $customerId): void
    {
        // @todo: delete function must be added to \XLite\Core\Cache\ExecuteCached
        $driver   = \XLite\Core\Cache::getInstance()->getDriver();
        $cacheKey = ExecuteCached::getCacheKey([self::class, $storeId, $customerId]);

        $driver->delete($cacheKey);
    }
}
