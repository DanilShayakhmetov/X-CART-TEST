<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\View\Tabs;

/**
 * Tabs related to localization
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class CartCheckout extends \XLite\View\Tabs\CartCheckout implements  \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();
        $tabs['shipping_settings']['template'] = 'modules/XC/Geolocation/settings/body_with_alert.twig';

        return $tabs;
    }
}
