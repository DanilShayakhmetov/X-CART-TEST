<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\View\Tabs;

/**
 * Tabs related to payment settings
 *
 *  ListChild (list="admin.center", zone="admin")
 */
abstract class SettingsTabsAbstract extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'google_feed';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'google_feed'   => [
                'weight'   => 100,
                'title'    => static::t('Feed generation & settings'),
                'widget' => 'XLite\Module\XC\GoogleFeed\View\Admin\GoogleFeed',
            ]
        ];
    }
}
