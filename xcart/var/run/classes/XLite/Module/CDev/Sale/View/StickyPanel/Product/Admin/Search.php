<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\StickyPanel\Product\Admin;

/**
 * Search product list sticky panel
 */
 class Search extends \XLite\Module\XC\BulkEditing\View\StickyPanel\Product\Admin\AAdmin implements \XLite\Base\IDecorator
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();
        $list['sale'] = [
            'class'    => 'XLite\Module\CDev\Sale\View\Button\Dropdown\ProductSale',
            'params'   => [
                'label'         => '',
                'style'         => 'always-enabled more-action icon-only hide-on-disable',
                'icon-style'    => 'fa fa-percent',
                'dropDirection' => 'dropup',
            ],
            'position' => 250,
        ];

        return $list;
    }
}
