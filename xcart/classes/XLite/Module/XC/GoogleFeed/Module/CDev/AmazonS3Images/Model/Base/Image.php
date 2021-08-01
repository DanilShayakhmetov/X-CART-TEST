<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\Module\CDev\AmazonS3Images\Model\Base;

/**
 * Storage abstract store
 *
 * @Decorator\Depend({"CDev\AmazonS3Images"})
 */
abstract class Image extends \XLite\Model\Base\Image implements \XLite\Base\IDecorator
{
    /**
     * Get URL
     *
     * @return string
     */
    public function getGoogleFeedURL()
    {
        if (static::STORAGE_S3 == $this->getStorageType()) {
            $url = $this->getURL();

        } else {
            $url = parent::getGoogleFeedURL();
        }

        return $url;
    }
}
