<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\View\Menu\Admin;


 class LeftMenu extends \XLite\Module\XC\GoogleFeed\View\Menu\Admin\LeftMenu implements \XLite\Base\IDecorator
{
    /**
     * Define items
     *
     * @return array
     */
    protected function defineItems()
    {
        $items = parent::defineItems();

        $items['sales_channels'][static::ITEM_CHILDREN]['facebook_marketing'] = [
            static::ITEM_TITLE  => static::t('Facebook Ads & Instagram Ads'),
            static::ITEM_TARGET => 'facebook_marketing',
            static::ITEM_WEIGHT => 300,
        ];

        return $items;
    }
}