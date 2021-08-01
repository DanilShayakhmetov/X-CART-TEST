<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\View\StickyPanel;

/**
 * Google shopping groups sticky panel
 */
class ShoppingGroup extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'shopping-group'  => [
                'class'    => 'XLite\Module\XC\GoogleFeed\View\Button\Dropdown\ShoppingGroup',
                'params'   => [
                    'label'         => 'Assign shopping group',
                    'style'         => 'more-action hide-on-disable hidden',
                    'dropDirection' => 'dropup',
                ],
                'position' => 100,
            ],
        ];
    }
}