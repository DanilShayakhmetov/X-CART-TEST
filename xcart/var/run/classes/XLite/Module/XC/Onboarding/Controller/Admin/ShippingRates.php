<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\Controller\Admin;

/**
 * Shipping rates page controller
 */
abstract class ShippingRates extends \XLite\Controller\Admin\ShippingRatesAbstract implements \XLite\Base\IDecorator
{
    protected function doActionUpdate()
    {
        parent::doActionUpdate();

        \XLite\Core\TmpVars::getInstance()->onboarding_shipping_changed = 1;
    }
}
