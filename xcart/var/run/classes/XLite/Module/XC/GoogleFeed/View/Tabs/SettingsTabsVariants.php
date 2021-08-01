<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\View\Tabs;

/**
 * Tabs related to payment settings
 */
 class SettingsTabsVariants extends \XLite\Module\XC\GoogleFeed\View\Tabs\SettingsTabsAbstract implements \XLite\Base\IDecorator
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'google_shopping_groups';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();
        $tabs['google_shopping_groups'] = [
            'weight' => 50,
            'title'  => static::t('Google Shopping Group'),
            'widget' => 'XLite\Module\XC\GoogleFeed\View\Admin\GoogleShoppingGroups',
        ];

        return $tabs;
    }
}
