<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe\View\StickyPanel\Payment;

/**
 * Payment method settings sticky panel
 */
class NonConfigured extends \XLite\View\StickyPanel\Payment\Settings
{
	/**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = [];
        $list['connect'] = $this->getWidget(
            [],
            'XLite\Module\XC\Stripe\View\Button\Connect'
        );
        $list = array_merge($list, parent::defineButtons());
        unset($list['save']);

        return $list;
    }
}

