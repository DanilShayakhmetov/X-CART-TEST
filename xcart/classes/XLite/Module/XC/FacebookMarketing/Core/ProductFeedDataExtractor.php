<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Core;

/**
 * ProductFeedDataExtractor
 */
class ProductFeedDataExtractor
{
    /**
     * @var \XLite\Module\XC\FacebookMarketing\Model\ProductFeed\IProductFeed
     */
    private $productFeed;

    /**
     * @var array
     */
    private $extractedData = [];

    /**
     * ProductFeedDataExtractor constructor.
     *
     * @param \XLite\Module\XC\FacebookMarketing\Model\ProductFeed\IProductFeed $productFeed
     */
    public function __construct($productFeed)
    {
        $this->productFeed = $productFeed;
    }

    /**
     * Return ProductFeed
     *
     * @return \XLite\Module\XC\FacebookMarketing\Model\ProductFeed\IProductFeed
     */
    public function getProductFeed()
    {
        return $this->productFeed;
    }

    /**
     * Return ExtractedData
     *
     * @return array
     */
    public function getExtractedData()
    {
        return $this->extractedData;
    }

    /**
     * Extract data from model
     *
     * @param \XLite\Model\AEntity $model
     */
    public function extractEntityData($model)
    {
        $this->extractedData = $this->getProductFeed()->extractEntityData($model);
    }
}