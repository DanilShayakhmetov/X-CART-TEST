<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request\Product;

use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;

class Update extends MailChimpRequest
{
    /**
     * @var string
     */
    protected $storeId;

    /**
     * @param string $storeId
     * @param string $productId
     * @param array  $productData
     */
    public function __construct($storeId, $productId, $productData)
    {
        parent::__construct('Updating product', 'patch', "ecommerce/stores/{$storeId}/products/{$productId}", $productData);

        $this->storeId = $storeId;
    }

    /**
     * @param string $storeId
     * @param array  $productData
     *
     * @return self
     */
    public static function getRequest($storeId, $productData): self
    {
        return new self($storeId, $productData['id'], $productData);
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

    /**
     * @return array|null
     */
    public function execute(): ?array
    {
        $result = parent::execute();
        if (!$result) {
            return Create::executeAction($this->storeId, $this->getArgs());
        }

        return $result;
    }
}
