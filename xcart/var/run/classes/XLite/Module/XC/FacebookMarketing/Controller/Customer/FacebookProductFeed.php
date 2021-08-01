<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Controller\Customer;

/**
 * FacebookProductFeed
 */
class FacebookProductFeed extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = ['target', 'key'];

    /**
     * @return bool
     */
    protected function checkAccess()
    {
        $key = \XLite\Core\Config::getInstance()->XC->FacebookMarketing->product_feed_key;
        return parent::checkAccess() && $key && \XLite\Core\Request::getInstance()->key === $key;
    }

    /**
     * @return \XLite\Module\XC\FacebookMarketing\Model\ProductFeed\IProductFeed
     */
    protected function getProductFeed()
    {
        return new \XLite\Module\XC\FacebookMarketing\Model\ProductFeed\AllProductsFeed;
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $productFeed = $this->getProductFeed();

        if (file_exists($productFeed->getStoragePath())) {
            $filename = $productFeed->getServiceName() . '.csv';
            $this->set('silent', true);
            $this->setSuppressOutput(true);
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . addslashes($filename) . '";');
            $this->readFeed($productFeed);
        } else {
            $this->markAsAccessDenied();
        }
    }

    /**
     * Read storage
     *
     * @param \XLite\Module\XC\FacebookMarketing\Model\ProductFeed\IProductFeed $productFeed
     *
     * @return void
     */
    protected function readFeed($productFeed)
    {
        $range = null;

        if (isset($_SERVER['HTTP_RANGE'])) {
            list($sizeUnit, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if ('bytes' == $sizeUnit) {
                list($range, $extra) = explode(',', $range, 2);
            }
        }

        $path = $productFeed->getStoragePath();
        $start = null;
        $length = intval(\Includes\Utils\FileManager::getFileSize($path));

        if ($range) {
            $size = $length;
            list($start, $end) = explode('-', $range, 2);
            $start = abs(intval($start));
            $end = abs(intval($end));

            $end = $end ? min($end, $size - 1) : ($size - 1);
            $start = (!$start || $end < $start) ? 0 : max($start, 0);

            if (0 < $start || ($size - 1) > $end) {
                header('HTTP/1.2 206 Partial Content', true, 206);
            }

            header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
            $length = ($end - $start + 1);
        }

        header('Accept-Ranges: bytes');
        header('Content-Length: ' . $length);

        if (!\XLite\Core\Request::getInstance()->isHead()) {
            $handle = @fopen($path, 'rb');

            if ($handle) {
                if (null !== $start) {
                    fseek($handle, $start);
                }

                if (isset($length)) {
                    while (!feof($handle) && $length > 0) {
                        $l = min(8192, $length);
                        print fread($handle, $l);
                        flush();
                        ob_flush();
                        $length -= 8192;
                    }
                } else {
                    while (!feof($handle)) {
                        print fread($handle, 8192);
                        flush();
                        ob_flush();
                    }
                }

                fclose($handle);
            }
        }
    }
}