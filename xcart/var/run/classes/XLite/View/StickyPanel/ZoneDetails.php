<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel;

/**
 * Panel for Zone details management form.
 */
class ZoneDetails extends \XLite\View\StickyPanel\ItemForm
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();

        $list['shipping_methods'] = new \XLite\View\Button\SimpleLink(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL => static::t('Back to Zones list'),
                \XLite\View\Button\AButton::PARAM_STYLE => 'action zone-back-button',
                \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('zones'),
            )
        );

        return $list;
    }
}
