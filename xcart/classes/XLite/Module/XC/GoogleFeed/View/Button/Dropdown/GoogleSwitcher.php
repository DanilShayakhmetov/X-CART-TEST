<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\View\Button\Dropdown;


/**
 * GoogleSwitcher
 */
class GoogleSwitcher extends \XLite\View\Button\Dropdown\ADropdown
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'feedEnable' => [
                'params'   => [
                    'action'     => 'google_product_feed_enable',
                    'label'      => 'Add to google product feed',
                    'style'      => 'action link list-action',
                    'icon-style' => 'fa fa-google state-on',
                ],
                'position' => 100,
            ],
            'feedDisable' => [
                'params'   => [
                    'action'     => 'google_product_feed_disable',
                    'label'      => 'Remove from google product feed',
                    'style'      => 'action link list-action',
                    'icon-style' => 'fa fa-google state-off',
                ],
                'position' => 200,
            ],
        ];
    }
}
