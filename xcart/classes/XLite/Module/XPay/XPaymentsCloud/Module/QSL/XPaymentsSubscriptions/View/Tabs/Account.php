<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Module\QSL\XPaymentsSubscriptions\View\Tabs;

/**
 * X-Payments Saved Cards tab
 * @Decorator\Depend({"QSL\XPaymentsSubscriptions", "XPay\XPaymentsCloud"})
 */
class Account extends \XLite\View\Tabs\Account implements \XLite\Base\IDecorator
{
    /**
     * Define tabs
     *
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();

        if (isset($tabs['x_payments_subscription'])) {
            $tabs['x_payments_subscription']['title'] = static::t('Subscriptions (legacy)');
        }

        return $tabs;
    }



}
