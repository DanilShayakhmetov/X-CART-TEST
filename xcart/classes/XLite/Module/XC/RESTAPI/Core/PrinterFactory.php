<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Core;

/**
 * API
 */
class PrinterFactory
{
    /**
     * Create printer
     *
     * @param \XLite\Module\XC\RESTAPI\Core\Schema\ASchema $schema Schema
     *
     * @return \XLite\Module\XC\RESTAPI\Core\Printer\APrinter
     */
    public static function create(\XLite\Module\XC\RESTAPI\Core\Schema\ASchema $schema)
    {
        $printer = null;

        foreach (static::getPrinterClasses() as $class) {
            if ($class::isOwn()) {
                $printer = new $class($schema);
                break;
            }
        }

        return $printer;
    }

    /**
     * Get printer classes
     *
     * @return array
     */
    protected static function getPrinterClasses()
    {
        return array(
            '\XLite\Module\XC\RESTAPI\Core\Printer\XML',
            '\XLite\Module\XC\RESTAPI\Core\Printer\YAML',
            '\XLite\Module\XC\RESTAPI\Core\Printer\JSONP',
            '\XLite\Module\XC\RESTAPI\Core\Printer\JSON',
        );
    }
}
