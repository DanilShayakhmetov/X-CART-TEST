<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Module\QSL\XPaymentsSubscriptions\Model;

use XLite\Module\XPay\XPaymentsCloud\Main as XPaymentsCloud;

/**
 * Class OrderItem
 * @Decorator\Depend({"XPay\XPaymentsCloud", "QSL\XPaymentsSubscriptions"})
 */
class OrderItem extends \XLite\Model\OrderItem implements \XLite\Base\IDecorator
{
    /**
     * Check if order item is subscription
     * Returns false if subscriptions should be processed by X-Payments Cloud module
     *
     * @return bool
     */
    public function isSubscription()
    {
        $result = parent::isSubscription();
        if (
            !\XLite\Core\Request::getInstance()->isCLI()
            && XPaymentsCloud::isUseXpaymentsCloudForSubscriptions()
        ) {
            $result = false;
        }

        return $result;
    }

    /**
     * Check if order item is subscription
     *
     * @return bool
     */
    public function isXpaymentsSubscription()
    {
        $result = parent::isXpaymentsSubscription();
        if (!XPaymentsCloud::isUseXpaymentsCloudForSubscriptions()) {
            $result = false;
        }

        return $result;
    }

}
