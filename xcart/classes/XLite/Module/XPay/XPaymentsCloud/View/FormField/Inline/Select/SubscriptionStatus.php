<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace XLite\Module\XPay\XPaymentsCloud\View\FormField\Inline\Select;

/**
 * SubscriptionStatus
 */
class SubscriptionStatus extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\Module\XPay\XPaymentsCloud\View\FormField\Select\SubscriptionStatus';
    }

    /**
     * Check - field is editable or not
     *
     * @return boolean
     */
    protected function hasSeparateView()
    {
        return false;
    }

}
