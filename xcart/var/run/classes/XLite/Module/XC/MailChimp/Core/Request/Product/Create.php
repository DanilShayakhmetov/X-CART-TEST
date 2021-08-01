<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request\Product;

use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;

class Create extends MailChimpRequest
{
    /**
     * @param string $storeId
     * @param array  $productData
     */
    public function __construct($storeId, $productData)
    {
        parent::__construct('Creating product', 'post', "ecommerce/stores/{$storeId}/products", $productData);
    }

    /**
     * @param string $storeId
     * @param array  $productData
     *
     * @return self
     */
    public static function getRequest($storeId, $productData): self
    {
        return new self($storeId, $productData);
    }

    /**
     * @param string $storeId
     * @param array  $productData
     *
     * @return mixed
     */
    public static function scheduleAction($storeId, $productData)
    {
        return self::getRequest($storeId, $productData)->schedule();
    }

    /**
     * @param string $storeId
     * @param array  $productData
     *
     * @return mixed
     */
    public static function executeAction($storeId, $productData)
    {
        return self::getRequest($storeId, $productData)->execute();
    }
}
