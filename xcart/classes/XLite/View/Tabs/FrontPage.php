<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

use XLite\Core\Auth;
use XLite\Model\Role\Permission;

/**
 * Tabs related to front page section
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class FrontPage extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'front_page';
        $list[] = 'banner_rotation';

        return $list;
    }

    /**
     * Check if the widget is visible
     *
     * @return boolean
     */
    public function isVisible()
    {
        return parent::isVisible() && !((bool) \XLite\Core\Request::getInstance()->id);
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = [];

        if (Auth::getInstance()->isPermissionAllowed(Permission::ROOT_ACCESS) || Auth::getInstance()->isPermissionAllowed('manage front page')) {
            $tabs['front_page'] = [
                'weight' => 100,
                'title' => static::t('Front page'),
                'template' => 'front_page/body.twig',
            ];
        }


        if (Auth::getInstance()->isPermissionAllowed('manage banners')) {
            $tabs['banner_rotation'] = [
                'weight'   => 200,
                'title'    => static::t('Banner rotation'),
                'template' => 'banner_rotation/body.twig',
            ];
        }


        return $tabs;
    }

    /**
     * Sorting the tabs according their weight
     *
     * @return array
     */
    protected function prepareTabs()
    {
        $tabs = parent::prepareTabs();

        if (!Auth::getInstance()->isPermissionAllowed('manage catalog')
            && Auth::getInstance()->isPermissionAllowed('manage banners')) {
            $tabs = array_filter($tabs, function($key) {
                return $key === 'banner_rotation';
            }, ARRAY_FILTER_USE_KEY);
        }

        return $tabs;
    }
}
