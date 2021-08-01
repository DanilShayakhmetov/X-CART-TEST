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
 * @Decorator\Depend ("XC\MultiVendor")
 * @ListChild (list="layout.header.bar.links.logged", weight="150", zone="customer")
 */
class BackendOrdersList extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    const PARAM_CAPTION = 'caption';

    /**
     * Return category Id to use
     *
     * @return integer
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
        return static::t('Orders');
    }

    /**
     * Only visible when vendor is logged in
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && \XLite\Core\Auth::getInstance()->isVendor();
    }

    /**
     * Get vendor interface link (admin area)
     *
     * @return string
     */
    protected function getBackendOrdersUrl()
    {
        return \XLite\Core\Converter::buildURL(
            'order_list',
            'search',
            ['filter_id' => 'recent'],
            \XLite::getAdminScript()
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/MultiVendor/layout/header/backend_orders.twig';
    }
}
