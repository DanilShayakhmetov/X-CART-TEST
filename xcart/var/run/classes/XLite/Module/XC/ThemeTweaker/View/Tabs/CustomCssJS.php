<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\Tabs;

/**
 * Tabs related to shipping
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class CustomCssJS extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'custom_css';
        $list[] = 'custom_js';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'custom_css' => [
                'weight' => 100,
                'title'  => static::t('Custom CSS'),
                'widget' => '\XLite\Module\XC\ThemeTweaker\View\Page\CustomCSS',
            ],
            'custom_js'  => [
                'weight' => 200,
                'title'  => static::t('Custom JavaScript'),
                'widget' => '\XLite\Module\XC\ThemeTweaker\View\Page\CustomJS',
            ],
        ];
    }
}
