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
class SchemaFactory
{
    /**
     * Create printer
     *
     * @return \XLite\Module\XC\RESTAPI\Core\Schema\ASchema
     */
    public static function create($code, $request, $method)
    {
        $schema = null;
        foreach (static::getSchemaClasses() as $class) {
            if ($class::isOwn($code)) {
                $schema = new $class($request, $method);
                break;
            }
        }

        return $schema;
    }

    /**
     * Get schema classes
     *
     * @return array
     */
    protected static function getSchemaClasses()
    {
        return array(
            '\XLite\Module\XC\RESTAPI\Core\Schema\Native',
            '\XLite\Module\XC\RESTAPI\Core\Schema\Complex',
        );
    }

    // }}}
}
