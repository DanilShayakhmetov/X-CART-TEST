<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu;

use XLite\Core\Cache\ExecuteCached;

/**
 * Abstract menu
 */
abstract class AMenu extends \XLite\View\AView
{
    /**
     * Items
     *
     * @var array
     */
    protected $items;

    /**
     * Define menu items
     *
     * @return array
     */
    abstract protected function defineItems();

    /**
     * Prepare items
     *
     * @param array $items Items
     *
     * @return array
     */
    abstract protected function prepareItems($items);

    /**
     * Mark selected
     *
     * @param array $items Items
     *
     * @return array
     */
    abstract protected function markSelected($items);

    /**
     * Check if items are present
     *
     * @return boolean
     */
    protected function hasItems()
    {
        $cacheParams   = $this->getCacheParameters();
        $cacheParams[] = 'hasItems';

        return $this->executeCached(function () {
            return count($this->getItems()) > 0;
        }, $cacheParams);
    }

     /**
      * Get menu items
      *
      * @return array
      */
    protected function getItems()
    {
        if (!isset($this->items)) {
            $cacheParams = [
                'getItems',
                get_class($this)
            ];

            $this->items = ExecuteCached::executeCachedRuntime(function() {
                $items = $this->defineItems();
                $items = $this->prepareItems($items);
                return $this->markSelected($items);
            }, $cacheParams);
        }

        return $this->items;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->hasItems();
    }
}
