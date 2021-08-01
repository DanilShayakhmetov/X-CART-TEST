<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\View\StickyPanel\Product\Admin;

/**
 * Search product list sticky panel
 */
 class Search extends \XLite\Module\XC\GoogleFeed\View\StickyPanel\Product\Admin\Search implements \XLite\Base\IDecorator
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();
        $list['facebook_feed'] = [
            'class'    => 'XLite\Module\XC\FacebookMarketing\View\Button\Dropdown\FacebookSwitcher',
            'params'   => [
                'label'         => '',
                'style'         => 'more-action icon-only hide-on-disable hidden',
                'icon-style'    => 'fa fa-facebook-official',
                'dropDirection' => 'dropup',
            ],
            'position' => 250,
        ];

        return $list;
    }
}