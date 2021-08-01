<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request\Product;

use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;

class Remove extends MailChimpRequest
{
    /**
     * @param string $storeId
     * @param string $productId
     */
    public function __construct($storeId, $productId)
    {
        parent::__construct('Removing product', 'delete', "ecommerce/stores/{$storeId}/products/{$productId}");
    }

    /**
     * @param string $storeId
     * @param string $productId
     *
     * @return self
     */
    public static function getRequest($storeId, $productId): self
    {
        return new self($storeId, $productId);
    }

    /**
     * @param string $storeId
     * @param string $productId
     *
     * @return mixed
     */
    public static function scheduleAction($storeId, $productId)
    {
        return self::getRequest($storeId, $productId)->schedule();
    }

    /**
     * @param string $storeId
     * @param string $productId
     *
     * @return mixed
     */
    public static function executeAction($storeId, $productId)
    {
        return self::getRequest($storeId, $productId)->execute();
    }
}
