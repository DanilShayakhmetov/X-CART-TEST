<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe;

use XLite\Core\Cache\ExecuteCached;

/**
 * Stripe module main class
 */
abstract class Main extends \XLite\Module\AModule
{
    /**
     * @return Object|\XLite\Model\Payment\Method
     */
    public static function getMethod()
    {
        return ExecuteCached::executeCachedRuntime(function () {
            return \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
                ->findOneBy(['service_name' => 'Stripe']);
        }, [__CLASS__, __FUNCTION__]);
    }
}