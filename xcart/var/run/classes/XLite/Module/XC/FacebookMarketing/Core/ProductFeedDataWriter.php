<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Core;

/**
 * ProductFeedDataWriter
 */
class ProductFeedDataWriter extends \XLite\Base
{
    /**
     * Write feed data
     *
     * @param \XLite\Module\XC\FacebookMarketing\Core\ProductFeedDataExtractor $extractor
     */
    public function writeFeedData($extractor)
    {
        if (!file_exists($this->getFeedFileName($extractor->getProductFeed()))) {
            $this->writeHeaderRow($extractor->getProductFeed());
        }

        $this->writeCSV(
            $this->getFeedFileHandle($extractor->getProductFeed()),
            $extractor->getExtractedData()
        );
    }

    /**
     * Write header row
     *
     * @param \XLite\Module\XC\FacebookMarketing\Model\ProductFeed\IProductFeed $productFeed
     */
    public function writeHeaderRow($productFeed)
    {
        $this->writeCSV(
            $this->getFeedFileHandle($productFeed, true),
            $productFeed->getHeaderRow()
        );
    }

    /**
     * @param \XLite\Module\XC\FacebookMarketing\Model\ProductFeed\IProductFeed $productFeed
     * @param bool                                                              $reset
     *
     * @return resource
     * @throws Exception\ProductFeedWriterException
     */
    protected function getFeedFileHandle($productFeed, $reset = false)
    {
        if (!file_exists(static::getGenerationDir())) {
            \Includes\Utils\FileManager::mkdirRecursive(static::getGenerationDir());
        }

        if (!($handle = fopen($this->getFeedFileName($productFeed), $reset ? 'w' : 'a'))) {
            throw new \XLite\Module\XC\FacebookMarketing\Core\Exception\ProductFeedWriterException();
        }

        return $handle;
    }

    /**
     * @param \XLite\Module\XC\FacebookMarketing\Model\ProductFeed\IProductFeed $productFeed
     *
     * @return string
     */
    protected function getFeedFileName($productFeed)
    {
        return static::getGenerationDir() . \Includes\Utils\FileManager::sanitizeFilename($productFeed->getServiceName());
    }

    /**
     * Write to csv file
     *
     * @param resource $handle
     * @param array    $data
     *
     * @throws Exception\ProductFeedWriterException
     */
    protected function writeCSV($handle, $data)
    {
        if (fputcsv($handle, $data) === false) {
            throw new \XLite\Module\XC\FacebookMarketing\Core\Exception\ProductFeedWriterException();
        }
        fclose($handle);
    }

    /**
     * Return directory for feeds generation
     *
     * @return string
     */
    public static function getGenerationDir()
    {
        return LC_DIR_TMP . 'product_feed' . LC_DS;
    }

    /**
     * Return directory for feeds generation
     *
     * @return string
     */
    public static function getDataDir()
    {
        return LC_DIR_DATA . 'product_feed' . LC_DS;
    }

    /**
     * Clear generation directory
     */
    public function clearGenerationDir()
    {
        if ($list = glob(static::getGenerationDir() . '*')) {
            foreach ($list as $path) {
                if (is_file($path)) {
                    \Includes\Utils\FileManager::deleteFile($path);
                }
            }
        }
    }

    /**
     * Clear generation directory
     */
    public function moveToDataDir()
    {
        if ($list = glob(static::getGenerationDir() . '*')) {
            foreach ($list as $path) {
                if (is_file($path)) {
                    $filename = \Includes\Utils\FileManager::sanitizeFilename(basename($path));
                    \Includes\Utils\FileManager::move($path, static::getDataDir() . $filename);
                }
            }
        }
    }
}