<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;


use XLite\Core\Cache\ExecuteCached;

class Logo extends \XLite\View\Image
{
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $sizes = $this->getSizes() ?: [0, 0];

        $this->widgetParams[self::PARAM_IMAGE]->setValue($this->getLogoImage());
        $this->widgetParams[self::PARAM_MAX_WIDTH]->setValue($sizes[0]);
        $this->widgetParams[self::PARAM_MAX_HEIGHT]->setValue($sizes[1]);
    }

    protected function isCacheAvailable()
    {
        return true;
    }

    protected function getCacheParameters()
    {
        return array_merge(
            parent::getCacheParameters(),
            [
                \XLite\Core\Database::getRepo('XLite\Model\Image\Common\Logo')->getVersion(),
                \XLite\Core\Database::getRepo('XLite\Model\ImageSettings')->getVersion(),
            ]
        );
    }

    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getLogoImage();
    }

    /**
     * @return \XLite\Model\Image\Common\Logo
     */
    protected function getLogoImage()
    {
        $cacheParams = [
            get_class($this),
            'logoImage'
        ];

        return ExecuteCached::executeCachedRuntime(function() {
            return \XLite\Core\Database::getRepo('XLite\Model\Image\Common\Logo')->getLogo();
        }, $cacheParams);
    }

    /**
     * @return array
     */
    protected function getSizes()
    {
        $cacheParams = [
            get_class($this),
            'getSizes'
        ];

        return ExecuteCached::executeCachedRuntime(function() {
            return \XLite\Logic\ImageResize\Generator::getImageSizes('XLite\Model\Image\Common\Logo', 'Default');
        }, $cacheParams);
    }

    /**
     * @inheritdoc
     */
    public function getProperties()
    {
        $props = parent::getProperties();

        foreach (['width', 'height'] as $key) {
            if (isset($props[$key])) {
                unset($props[$key]);
            }
        }

        return $props;
    }

    public function getAlt()
    {
        return $this->getLogoAlt();
    }
}
