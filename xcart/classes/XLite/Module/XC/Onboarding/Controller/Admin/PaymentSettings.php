<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\Controller\Admin;

abstract class PaymentSettings extends \XLite\Controller\Admin\PaymentSettings implements \XLite\Base\IDecorator
{
    protected function doActionAdd()
    {
        parent::doActionAdd();

        $this->markAsChangedForOnboarding();
    }

    protected function doActionRemove()
    {
        parent::doActionRemove();

        $this->markAsChangedForOnboarding();
    }

    protected function doActionEnable()
    {
        //Mark before parent call because dispatchAJAXEnable() calls die
        $this->markAsChangedForOnboarding();

        parent::doActionEnable();
    }

    protected function doActionDisable()
    {
        //Mark before parent call because dispatchAJAXEnable() calls die
        $this->markAsChangedForOnboarding();

        parent::doActionDisable();
    }

    protected function doActionAddOfflineMethod()
    {
        parent::doActionAddOfflineMethod();

        $this->markAsChangedForOnboarding();
    }

    protected function markAsChangedForOnboarding()
    {
        \XLite\Core\TmpVars::getInstance()->onboarding_payment_changed = 1;
    }
}
