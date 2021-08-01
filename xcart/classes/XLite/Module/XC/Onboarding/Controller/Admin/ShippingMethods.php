<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\Controller\Admin;

abstract class ShippingMethods extends \XLite\Controller\Admin\ShippingMethods implements \XLite\Base\IDecorator
{
    public function doActionAdd()
    {
        //Parent method does redirect so mark before it's called
        $this->markAsChangedForOnboarding();

        parent::doActionAdd();
    }

    protected function doActionUpdateItemsList()
    {
        parent::doActionUpdateItemsList();

        $this->markAsChangedForOnboarding();
    }

    protected function markAsChangedForOnboarding()
    {
        \XLite\Core\TmpVars::getInstance()->onboarding_shipping_changed = 1;
    }
}
