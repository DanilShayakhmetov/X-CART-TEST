<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\View\Button\Dropdown;


/**
 * FacebookSwitcher
 */
class FacebookSwitcher extends \XLite\View\Button\Dropdown\ADropdown
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
                    'action'     => 'facebook_product_feed_enable',
                    'label'      => 'Enable facebook product feed',
                    'style'      => 'action link list-action',
                    'icon-style' => 'fa fa-facebook-official state-on',
                ],
                'position' => 100,
            ],
            'feedDisable' => [
                'params'   => [
                    'action'     => 'facebook_product_feed_disable',
                    'label'      => 'Disable facebook product feed',
                    'style'      => 'action link list-action',
                    'icon-style' => 'fa fa-facebook-official state-off',
                ],
                'position' => 200,
            ],
        ];
    }
}
