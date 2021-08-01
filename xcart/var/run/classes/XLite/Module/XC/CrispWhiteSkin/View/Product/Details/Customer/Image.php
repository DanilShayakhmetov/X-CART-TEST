<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Product\Details\Customer;

/**
 * Image
 */
 class Image extends \XLite\Module\XC\ThemeTweaker\View\Product\Details\Customer\Image implements \XLite\Base\IDecorator
{
    /**
     * Return true if image is zoomable
     *
     * @param $image \XLite\Model\Image\Product\Image
     *
     * @return boolean
     */
    protected function isImageZoomable($image)
    {
        return $image->getWidth() > $this->getZoomWidth() || $image->getHeight() > $this->getZoomHeight();
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'product/details/parts/cloud-zoom.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'js/add_to_cart.js';

        return $list;
    }

    /**
     * Get zoom layer width
     *
     * @return integer
     */
    protected function getZoomWidth()
    {
        return \XLite::getController()->getDefaultMaxImageSize(true);
    }

    /**
     * Get zoom layer height
     *
     * @return integer
     */
    protected function getZoomHeight()
    {
        return \XLite::getController()->getDefaultMaxImageSize(false);
    }
}
