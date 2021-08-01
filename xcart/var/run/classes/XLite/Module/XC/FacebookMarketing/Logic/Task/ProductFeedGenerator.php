<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Logic\Task;


use XLite\Module\XC\FacebookMarketing\Core\ProductFeedDataExtractor;
use XLite\Module\XC\FacebookMarketing\Core\ProductFeedDataWriter;

class ProductFeedGenerator extends \XLite\Base\Singleton
{
    const LOCK_FILE = '.productFeedGenerationStartedLock';

    protected $productFeed;
    protected $periodicTaskModel;

    /**
     * Generate
     *
     * @return void
     */
    public function generate()
    {
        $this->setGenerationStartedLock();
        ProductFeedDataWriter::getInstance()->clearGenerationDir();
        $this->generateProductFeed();
        ProductFeedDataWriter::getInstance()->moveToDataDir();
        $this->unlockGeneration();
    }

    /**
     * Generate product feed
     *
     * @return void
     */
    protected function generateProductFeed()
    {
        $productsCount = \XLite\Core\Database::getRepo('XLite\Model\Product')->countForFacebookProductFeed();
        $chunkLength = \XLite\Module\XC\FacebookMarketing\Core\EventListener\ProductFeedGeneration::CHUNK_LENGTH;
        $chunksCount = ceil($productsCount / $chunkLength);

        for ($i = 0; $i < $chunksCount; $i++) {
            \XLite\Core\Database::getEM()->clear();
            $this->productFeed = null;
            if (isset($this->periodicTaskModel)) {
                $this->periodicTaskModel->mergeModel();
            }

            foreach ($this->getIterator($i * $chunkLength) as $data) {
                if (!empty($data[0])) {
                    $model = $data[0];

                    $this->processModel($model);
                }
            }
        }
    }

    /**
     * Process product for feed
     *
     * @param \XLite\Model\AEntity $model
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
     * Return products feed
     *
     * @return \XLite\Module\XC\FacebookMarketing\Model\ProductFeed\AllProductsFeed
     */
    public function getProductFeed()
    {
        if (!$this->productFeed) {
            $this->productFeed = new \XLite\Module\XC\FacebookMarketing\Model\ProductFeed\AllProductsFeed;
        }

        return $this->productFeed;
    }

    /**
     * Get iterator
     *
     * @param int $position
     *
     * @return \XLite\Module\CDev\XMLSitemap\Logic\SitemapIterator
     */
    protected function getIterator($position)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')->getFacebookProductFeedIterator($position);
    }

    /**
     * Create lock file
     *
     * @return void
     */
    protected function setGenerationStartedLock()
    {
        if(file_exists(LC_DIR_TMP . self::LOCK_FILE)) {
            \XLite\Logger::getInstance()->log('Previous Product Feed generation died without resetting lock file');
        }
        file_put_contents(LC_DIR_TMP . self::LOCK_FILE, '');
    }

    /**
     * Remove lock file
     *
     * @return void
     */
    protected function unlockGeneration()
    {
        unlink(LC_DIR_TMP . self::LOCK_FILE);
    }

    /**
     * Set periodic task entity
     *
     * @param $task
     */
    public function setPeriodicTaskModel($task)
    {
        $this->periodicTaskModel = $task;
    }
}