<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\StickyPanel;

/**
 * Order panel for subscription orders
 */
class SubscriptionOrdersPanel extends \XLite\View\StickyPanel\Order\Admin\AAdmin
{
    /**
     * Remove 'export' button from panel
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();

        if (isset($list['export'])) {
            unset($list['export']);
        }

        return $list;
    }

}
