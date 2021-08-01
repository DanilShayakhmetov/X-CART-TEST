<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\Model;

use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Converter;

/**
 * Menu
 *
 * @Decorator\Depend ("CDev\SimpleCMS")
 */
 class Menu extends \XLite\Module\CDev\Sale\Model\Menu implements \XLite\Base\IDecorator
{
    use ExecuteCachedTrait;

    const DEFAULT_NEW_ARRIVALS = '{new arrivals}';
    const DEFAULT_COMING_SOON  = '{coming soon}';

    /**
     * Defines the resulting link values for the specific link values
     * for example: {home}
     *
     * @return array
     */
    protected function defineLinkURLs()
    {
        $list = parent::defineLinkURLs();

        $list += $this->executeCachedRuntime(function () {
            return [
                static::DEFAULT_NEW_ARRIVALS => Converter::buildURL('new_arrivals'),
                static::DEFAULT_COMING_SOON  => Converter::buildURL('coming_soon'),
            ];
        }, 'product_advisor');

        return $list;
    }

    /**
     * Defines the link controller class names for the specific link values
     * for example: {home}
     *
     * @return array
     */
    protected function defineLinkControllers()
    {
        $list = parent::defineLinkControllers();

        $list[static::DEFAULT_COMING_SOON] = '\XLite\Module\CDev\ProductAdvisor\Controller\Customer\ComingSoon';
        $list[static::DEFAULT_NEW_ARRIVALS] = '\XLite\Module\CDev\ProductAdvisor\Controller\Customer\NewArrivals';

        return $list;
    }
}
