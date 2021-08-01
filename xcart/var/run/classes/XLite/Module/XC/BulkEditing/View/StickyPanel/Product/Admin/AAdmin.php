<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\StickyPanel\Product\Admin;

/**
 * Product list sticky panel
 */
abstract class AAdmin extends \XLite\Module\XC\FacebookMarketing\View\StickyPanel\Product\Admin\Search implements \XLite\Base\IDecorator
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();
        $list['bulk_edit'] = [
            'class'    => 'XLite\Module\XC\BulkEditing\View\Button\Product',
            'params'   => [
                'style'          => 'more-action always-enabled',
                'dropDirection'  => 'dropup',
            ],
            'position' => 50,
        ];

        return $list;
    }
}
