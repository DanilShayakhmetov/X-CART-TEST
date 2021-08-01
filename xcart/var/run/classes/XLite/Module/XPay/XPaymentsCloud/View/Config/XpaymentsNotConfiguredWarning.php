<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Config;

use XLite\Module\XPay\XPaymentsCloud\Main;

class XpaymentsNotConfiguredWarning extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XPay/XPaymentsCloud/product/xpayments_not_configured_warning.twig';
    }

    /**
     * @return bool
     */
    protected function isXpaymentsSubscriptionsConfiguredAndActive()
    {
        return Main::isXpaymentsSubscriptionsConfiguredAndActive();
    }

    /**
     * @return int
     */
    protected function getXpaymentsPaymentMethodId()
    {
        return Main::getPaymentMethod()->getMethodId();
    }

}
