<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\StickyPanel;


/**
 * Sale discounts list sticky panel
 */
class SaleDiscounts extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        $list['recalculateQD'] = $this->getRecalculateQDWidget();

        return $list;
    }

    /**
     * Get "recalculateQD" widget
     *
     * @return \XLite\View\AView
     */
    protected function getRecalculateQDWidget()
    {
        return $this->getWidget(
            [
                'template' => 'modules/CDev/Sale/recalculate_qd_link/body.twig',
                'link'     => $this->buildURL('cache_management')
            ]
        );
    }
}