<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Module\XC\Cloud;

/**
 * X-Cart Cloud operations 
 *
 * @Decorator\Depend({"XPay\XPaymentsCloud","XC\Cloud"})
 */
abstract class Main extends \XLite\Module\XC\Cloud\Main implements \XLite\Base\IDecorator
{
    /**
     * Trigger trial event
     *
     * @return void
     */
    public static function triggerTrialEvent()
    {
        parent::triggerTrialEvent();

        // Register X-Cart Cloud domain (and path if any)
        \XLite\Module\XPay\XPaymentsCloud\Main::registerCloudShopUrl();
    }
}
