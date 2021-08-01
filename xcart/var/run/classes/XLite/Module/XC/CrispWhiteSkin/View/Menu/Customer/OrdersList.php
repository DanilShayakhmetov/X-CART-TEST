<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Menu\Customer;

/**
 * Orders list menu item
 *
 * @ListChild (list="layout.header.bar.links.logged", weight="300", zone="customer")
 */
class OrdersList extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    const PARAM_CAPTION = 'caption';

    /**
     * @return string
     */
    protected function getCaption()
    {
        return $this->getParam(static::PARAM_CAPTION);
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_CAPTION => new \XLite\Model\WidgetParam\TypeString('Link caption', $this->getDefaultCaption()),
        ];
    }

    /**
     * @return string
     */
    protected function getDefaultCaption()
    {
        return static::t('Orders list');
    }

    /**
     * @return string
     */
    protected function getOrdersListUrl()
    {
        return $this->buildURL('order_list');
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout/header/orders.twig';
    }
}
