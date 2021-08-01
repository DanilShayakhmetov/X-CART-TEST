<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\View\StickyPanel\ItemsList;

/**
 * Tag items list's sticky panel
 */
class Tag extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'delete' => [
                'class'    => 'XLite\View\Button\DeleteSelected',
                'params'   => [
                    'label'      => static::t('Delete'),
                    'style'      => 'more-action hide-on-disable hidden',
                    'icon-style' => 'fa fa-trash-o',
                ],
                'position' => 100,
            ],
        ];
    }
}
