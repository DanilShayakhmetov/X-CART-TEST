<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages;

use Includes\Utils\Module\Manager;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Check - vendor messaging allowed or not
     *
     * @return boolean
     */
    public static function isVendorAllowedToCommunicate()
    {
        return static::isMultivendor() && (!static::isWarehouse() || static::isVendorAllowedToCommunicateInWarehouse());
    }

    /**
     * Returns warehouse mode status
     *
     * @return boolean
     */
    public static function isWarehouse()
    {
        return static::isMultivendor() && \XLite\Module\XC\MultiVendor\Main::isWarehouseMode();
    }

    /**
     * Returns warehouse mode status
     *
     * @return boolean
     */
    public static function isMultivendor()
    {
        return Manager::getRegistry()->isModuleEnabled('XC', 'MultiVendor');
    }

    /**
     * Check is vendor messaging allowed in warehouse mode
     *
     * @return boolean
     */
    public static function isVendorAllowedToCommunicateInWarehouse()
    {
        return \XLite\Core\Config::getInstance()->XC->VendorMessages->allow_vendor_communication;
    }

    /**
     * Allow disputes or not
     *
     * @return boolean
     */
    public static function isAllowDisputes()
    {
        return static::isMultivendor() && (!static::isWarehouse() || static::isVendorAllowedToCommunicate());
    }

}
