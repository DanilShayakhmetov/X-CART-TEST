<?php

namespace XLite\Module\Kliken\GoogleAds\View\Menu\Admin;

abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu implements \XLite\Base\IDecorator
{
    protected function defineItems()
    {
        $items = parent::defineItems();

        $items['sales_channels'][self::ITEM_CHILDREN]['google_kkads'] = [
            self::ITEM_TITLE => static::t('Google Ads'),
            self::ITEM_WEIGHT => 0,
            self::ITEM_TARGET => \XLite\Module\Kliken\GoogleAds\Logic\Helper::PAGE_SLUG,
        ];

        return $items;
    }
}
