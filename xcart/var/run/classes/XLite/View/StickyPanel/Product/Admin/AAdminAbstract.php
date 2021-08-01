<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\Product\Admin;

/**
 * Abstract product panel for admin interface
 */
abstract class AAdminAbstract extends \XLite\View\StickyPanel\Product\AProduct
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'status' => [
                'class'    => 'XLite\View\Button\Dropdown\Status',
                'params'   => [
                    'label'         => '',
                    'style'         => 'always-enabled more-action icon-only hide-on-disable',
                    'icon-style'    => 'fa fa-power-off iconfont',
                    'dropDirection' => 'dropup',
                ],
                'position' => 200,
            ],
            'clone' => [
                'class'    => 'XLite\View\Button\CloneSelected',
                'params'   => [
                    'label'      => '',
                    'style'      => 'more-action icon-only hide-on-disable hidden',
                    'icon-style' => 'fa fa-copy',
                ],
                'position' => 300,
            ],
            'delete' => [
                'class'    => 'XLite\View\Button\DeleteSelected',
                'params'   => [
                    'label'      => '',
                    'style'      => 'more-action icon-only hide-on-disable hidden',
                    'icon-style' => 'fa fa-trash-o',
                ],
                'position' => 400,
            ],
        ];
    }

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        $list['export'] = $this->getWidget(
            [],
            'XLite\View\Button\ItemsExport\Product'
        );

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'sticky_panel/product_list/script.js'
        ]);
    }
}
