<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\Controller\Admin;

class ShippingSettings extends \XLite\Controller\Admin\ShippingSettings implements \XLite\Base\IDecorator
{
    protected function doActionSwitch()
    {
        parent::doActionSwitch();

        $this->markAsChangedForOnboarding();
    }

    public function doActionUpdate()
    {
        parent::doActionUpdate();

        $this->markAsChangedForOnboarding();
    }

    protected function markAsChangedForOnboarding()
    {
        \XLite\Core\TmpVars::getInstance()->onboarding_shipping_changed = 1;
    }
}