<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Module\QSL\XPaymentsSubscriptions\View;

use XLite\Module\XPay\XPaymentsCloud\Main as XPaymentsCloud;

/**
 * Class Price
 * @Decorator\Depend({"XPay\XPaymentsCloud", "QSL\XPaymentsSubscriptions"})
 */
class Price extends \XLite\View\Price implements \XLite\Base\IDecorator
{
    /**
     * Check if is need to show subscriptions info
     * Returns false if subscriptions should be processed by X-Payments Cloud module
     *
     * @return boolean
     */
    protected function isShowSubscriptionInfo()
    {
        $result = parent::isShowSubscriptionInfo();
        if (XPaymentsCloud::isUseXpaymentsCloudForSubscriptions()) {
            $result = false;
        }

        return $result;
    }

    /**
     * Check if is need to show subscriptions info
     *
     * @return boolean
     */
    protected function isShowXpaymentsSubscriptionInfo()
    {
        $result = parent::isShowXpaymentsSubscriptionInfo();
        if (!XPaymentsCloud::isUseXpaymentsCloudForSubscriptions()) {
            $result = false;
        }

        return $result;
    }

}
