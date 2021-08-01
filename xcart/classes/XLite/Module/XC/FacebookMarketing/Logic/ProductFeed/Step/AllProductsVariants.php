<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Logic\ProductFeed\Step;
use XLite\Module\XC\FacebookMarketing\Core\ProductFeedDataExtractor;
use XLite\Module\XC\FacebookMarketing\Core\ProductFeedDataWriter;

/**
 * Products
 *
 * @Decorator\Depend({"XC\ProductVariants", "XC\GoogleFeed"})
 */
class AllProductsVariants extends \XLite\Module\XC\FacebookMarketing\Logic\ProductFeed\Step\AllProducts implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     *
     * @param $model \XLite\Model\Product
     */
    protected function processModel(\XLite\Model\AEntity $model)
    {
        if ($model->hasVariants()) {
            $isProductOutOfStock = $model->getAmount() <= 0 && $model->getInventoryEnabled();

            foreach ($model->getVariants() as $variant) {
                $isVariantOutOfStock = $variant->getDefaultAmount()
                    ? $isProductOutOfStock
                    : $variant->getAmount() <= 0;

                $shouldSkipEntity = 'N' === \XLite\Core\Config::getInstance()->XC->FacebookMarketing->include_out_of_stock
                    && $isVariantOutOfStock;

                if (!$shouldSkipEntity) {
                    $extractor = new ProductFeedDataExtractor($this->getProductFeed());
                    $extractor->extractEntityData($variant);

                    ProductFeedDataWriter::getInstance()->writeFeedData($extractor);
                }
            }

        } else {
            parent::processModel($model);
        }
    }
}