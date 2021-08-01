<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics;

use XLite\Module\CDev\GoogleAnalytics\Logic\Action;
use XLite\Module\CDev\GoogleAnalytics\Logic\ActionsStorage;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * @inheritdoc
     */
    public static function init()
    {
        parent::init();

        ActionsStorage::getInstance()->addAction(
            'purchaseAction',
            new Action\Purchase()
        );

        ActionsStorage::getInstance()->addAction(
            'checkoutEnteredAction',
            new Action\CheckoutInit()
        );

        ActionsStorage::getInstance()->addAction(
            'checkoutComplete',
            new Action\CheckoutComplete()
        );
    }

    /**
     * @return bool
     */
    public static function useUniversalAnalytics()
    {
        return \XLite\Core\Config::getInstance()->CDev
            && \XLite\Core\Config::getInstance()->CDev->GoogleAnalytics
            && \XLite\Core\Config::getInstance()->CDev->GoogleAnalytics->ga_account
            && 'U' === \XLite\Core\Config::getInstance()->CDev->GoogleAnalytics->ga_code_version;
    }

    /**
     * @return bool
     */
    public static function isECommerceEnabled()
    {
        return static::useUniversalAnalytics()
            && \XLite\Core\Config::getInstance()->CDev->GoogleAnalytics->ecommerce_enabled;
    }

    /**
     * @return bool
     */
    public static function isPurchaseImmediatelyOnSuccess()
    {
        return !\XLite\Core\Config::getInstance()->CDev->GoogleAnalytics->purchase_only_on_paid;
    }

    /**
     * @return bool
     */
    public static function isDebugMode()
    {
        return \XLite\Core\Config::getInstance()->CDev->GoogleAnalytics->debug_mode;
    }

    /**
     * @return boolean
     */
    public static function hasGdprRelatedActivity()
    {
        return true;
    }
}
