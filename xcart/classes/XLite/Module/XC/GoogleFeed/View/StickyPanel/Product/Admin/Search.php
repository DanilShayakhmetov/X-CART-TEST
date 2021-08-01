<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\View\StickyPanel\Product\Admin;

/**
 * Search product list sticky panel
 */
class Search extends \XLite\View\StickyPanel\Product\Admin\AAdmin implements \XLite\Base\IDecorator
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();
        $list['google_feed'] = [
            'class'    => 'XLite\Module\XC\GoogleFeed\View\Button\Dropdown\GoogleSwitcher',
            'params'   => [
                'label'         => '',
                'style'         => 'more-action icon-only hide-on-disable hidden',
                'icon-style'    => 'fa fa-google',
                'useCaretButton' => false,
                'dropDirection' => 'dropup',
            ],
            'position' => 250,
        ];

        return $list;
    }
}