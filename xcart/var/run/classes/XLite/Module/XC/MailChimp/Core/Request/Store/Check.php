<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request\Store;

use XLite\Core\Cache\ExecuteCached;
use XLite\Core\Database;
use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;
use XLite\Module\XC\MailChimp\Model\Store;

class Check extends MailChimpRequest
{
    /**
     * @param string $storeId
     */
    public function __construct($storeId)
    {
        parent::__construct('Checking store', 'get', "ecommerce/stores/{$storeId}", ['fields' => 'id']);
    }

    /**
     * @param string $storeId
     *
     * @return self
     */
    public static function getRequest($storeId): self
    {
        return new self($storeId);
    }

    /**
     * @param string $storeId
     *
     * @return mixed
     */
    public static function executeAction($storeId)
    {
        return ExecuteCached::executeCached(
            static function () use ($storeId) {
                return self::getRequest($storeId)->execute();
            },
            [self::class, $storeId]
        );
    }

    /**
     * @param string $storeId
     * @param string $customerId
     */
    public static function dropActionCache($storeId): void
    {
        // @todo: delete function must be added to \XLite\Core\Cache\ExecuteCached
        $driver   = \XLite\Core\Cache::getInstance()->getDriver();
        $cacheKey = ExecuteCached::getCacheKey([self::class, $storeId]);

        $driver->delete($cacheKey);
    }

    /**
     * @param $storeId
     *
     * @return bool
     */
    public static function isStoreExistsInDB($storeId): bool
    {
        return (bool) Database::getRepo(Store::class)->find($storeId);
    }
}
