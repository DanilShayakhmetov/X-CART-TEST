<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\View\StickyPanel\ItemsList;


class GlobalTab extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define additional buttons
     * These buttons will be composed into dropup menu.
     * The divider button is also available: \XLite\View\Button\Dropdown\Divider
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();

        $list['global_update'] = [
            'class'    => '\XLite\View\FormField\Input\Checkbox\Simple',
            'params'   => [
                'label'     => 'Apply sort and view settings for all products',
                'fieldName' => 'global_update',
                'value'     => false
            ],
            'position' => 100,
        ];

        return $list;
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        $class = parent::getClass();
        $class = trim($class) . ' global-tabs-sticky-panel';

        return $class;
    }
}