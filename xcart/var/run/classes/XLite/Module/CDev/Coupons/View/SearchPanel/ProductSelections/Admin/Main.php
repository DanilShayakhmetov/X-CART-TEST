<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\SearchPanel\ProductSelections\Admin;

/**
 * Main admin orders list search panel
 */
class Main extends \XLite\View\SearchPanel\ProductSelections\Admin\Main
{
    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\Module\CDev\Coupons\View\Form\ItemsList\ProductSelection\Search';
    }
}
