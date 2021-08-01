<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Core\Printer;

/**
 * Abstract printer
 */
abstract class APrinter extends \XLite\Base
{
    /**
     * Schema 
     * 
     * @var \XLite\Module\XC\RESTAPI\Core\Schema\ASchema
     */
    protected $schema;

    /**
     * Print output 
     * 
     * @param mixed $data Data
     *  
     * @return void
     */
    abstract public function printOutput($data);

    /**
     * Check - schema is own this request or not
     * 
     * @return boolean
     */
    public static function isOwn()
    {
        return false;
    }

    /**
     * Constructor
     * 
     * @param \XLite\Module\XC\RESTAPI\Core\Schema\ASchema $schema Schema
     *  
     * @return void
     */
    public function __construct(\XLite\Module\XC\RESTAPI\Core\Schema\ASchema $schema)
    {
        $this->schema = $schema;
    }

}
