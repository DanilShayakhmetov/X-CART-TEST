<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Get theme files directory
     *
     * @return string
     */
    public static function getThemeDir()
    {
        return LC_DIR_VAR . 'theme' . LC_DS;
    }

    public static function getDumpOrder()
    {
        $orderId = \XLite\Core\TmpVars::getInstance()->themeTweakerDumpOrderId;
        $order   = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderId);

        if (null === $order) {
            $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->findDumpOrder();
            if ($order) {
                \XLite\Core\TmpVars::getInstance()->themeTweakerDumpOrderId = $order->getOrderId();
            }
        }

        return $order;
    }

    public static function isOrderNotification($templateDirectory)
    {
        return in_array(
            $templateDirectory,
            [
                'order_advanced_changed',
                'order_canceled',
                'order_changed',
                'order_created',
                'order_failed',
                'order_processed',
                'order_shipped',
                'order_tracking_information',
            ],
            true
        );
    }
}
