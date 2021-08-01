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
 */
class AllProducts extends \XLite\Logic\ARepoStep
{
    use \XLite\Core\Cache\ExecuteCachedTrait;

    /**
     * Return products feed
     *
     * @return \XLite\Module\XC\FacebookMarketing\Model\ProductFeed\AllProductsFeed
     */
    public function getProductFeed()
    {
        return $this->executeCachedRuntime(function () {
            return new \XLite\Module\XC\FacebookMarketing\Model\ProductFeed\AllProductsFeed;
        });
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return $this->getRepository()->countForFacebookProductFeed();
    }

    /**
     * Run step
     *
     * @return boolean
     */
    public function run()
    {
        try {
            return parent::run();
        } catch (\XLite\Module\XC\FacebookMarketing\Core\Exception\ProductFeedWriterException $e) {
            return false;
        }
    }

    /**
     * @inheritdoc
     *
     * @param $model \XLite\Model\Product
     */
    protected function processModel(\XLite\Model\AEntity $model)
    {
        $shouldSkipEntity = 'N' === \XLite\Core\Config::getInstance()->XC->FacebookMarketing->include_out_of_stock
            && $model->isOutOfStock();

        if (!$shouldSkipEntity) {
            $extractor = new ProductFeedDataExtractor($this->getProductFeed());
            $extractor->extractEntityData($model);

            ProductFeedDataWriter::getInstance()->writeFeedData($extractor);
        }
    }

    /**
     * @inheritdoc
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product');
    }

    /**
     * @inheritdoc
     */
    protected function getItems($reset = false)
    {
        if (!isset($this->items) || $reset) {
            $this->items = $this->getRepository()->getFacebookProductFeedIterator($this->position);

            $this->items->rewind();
        }

        return $this->items;
    }

}