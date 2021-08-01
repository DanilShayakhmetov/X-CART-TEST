<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request\Cart;

use XLite\Core\Cache\ExecuteCached;
use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;

class Check extends MailChimpRequest
{
    /**
     * @param string $storeId
     * @param string $cartId
     */
    public function __construct($storeId, $cartId)
    {
        parent::__construct('Checking cart', 'get', "ecommerce/stores/{$storeId}/carts/{$cartId}", ['fields' => 'id']);
    }

    /**
     * @param string $storeId
     * @param string $cartId
     *
     * @return self
     */
    public static function getRequest($storeId, $cartId): self
    {
        return new self($storeId, $cartId);
    }

    /**
     * @param string $storeId
     * @param string $cartId
     *
     * @return mixed
     */
    public static function executeAction($storeId, $cartId)
    {
        return ExecuteCached::executeCached(
            static function () use ($storeId, $cartId) {
                return self::getRequest($storeId, $cartId)->execute();
            },
            [self::class, $storeId, $cartId]
        );
    }

    /**
     * @param string $storeId
     * @param string $cartId
     */
    public static function dropActionCache($storeId, $cartId): void
    {
        // @todo: delete function must be added to \XLite\Core\Cache\ExecuteCached
        $driver   = \XLite\Core\Cache::getInstance()->getDriver();
        $cacheKey = ExecuteCached::getCacheKey([self::class, $storeId, $cartId]);

        $driver->delete($cacheKey);
    }
}
