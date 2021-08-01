<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping;

/**
 * Free shipping module
 */
abstract class Main extends \XLite\Module\AModule
{
    /**
     * Switch service free shipping modules
     *
     * @param bool $enabled enabled
     */
    public static function switchFreeShippingMethods($enabled)
    {
        $shippingMethods = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->findBy(['code' => 'FREESHIP', 'free' => true]);
        foreach ($shippingMethods as $method) {
            $method->setEnabled($enabled);
        }

        $shippingMethods = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->findBy(['code' => 'FIXEDFEE']);
        foreach ($shippingMethods as $method) {
            $method->setEnabled($enabled);
        }

        \XLite\Core\Database::getEM()->flush();
    }
}
