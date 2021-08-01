<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\Module\XPay\XPaymentsCloud;

/**
 * Payment step
 *
 * @Decorator\Depend("XPay\XPaymentsCloud")
 */
class Payment extends \XLite\Module\XC\Onboarding\View\WizardStep\Payment implements \XLite\Base\IDecorator
{
    const XPAYMENT_SORT = 20;

    /**
     * @return array
     */
    protected function getOnlineWidgets()
    {
        $widgets = parent::getOnlineWidgets();
        $widgets[self::XPAYMENT_SORT] = View\Onboarding\Payment::class;

        return $widgets;
    }
}