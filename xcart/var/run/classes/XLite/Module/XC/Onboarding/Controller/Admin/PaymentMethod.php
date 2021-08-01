<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\Controller\Admin;

abstract class PaymentMethod extends \XLite\Module\XC\Stripe\Controller\Admin\PaymentMethod implements \XLite\Base\IDecorator
{
    protected function doActionUpdate()
    {
        parent::doActionUpdate();

        \XLite\Core\TmpVars::getInstance()->onboarding_payment_changed = 1;
    }
}
