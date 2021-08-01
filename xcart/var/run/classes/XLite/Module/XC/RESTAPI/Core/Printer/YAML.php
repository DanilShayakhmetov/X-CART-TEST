<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Core\Printer;

/**
 * YAML printer
 */
class YAML extends \XLite\Module\XC\RESTAPI\Core\Printer\Base\HTTP
{
    /**
     * Check - schema is own this request or not
     * 
     * @return boolean
     */
    public static function isOwn()
    {
        return !empty($_SERVER['HTTP_ACCEPT'])
            && in_array($_SERVER['HTTP_ACCEPT'], array('text/yaml', 'application/x-yaml'))
            && empty(\XLite\Core\Request::getInstance()->callback);
    }

    /**
     * Print output
     *
     * @param mixed $data Data
     *
     * @return void
     */
    public function printOutput($data)
    {
        header('Content-Type: ' . $_SERVER['HTTP_ACCEPT'] . ';charset=utf-8');

        parent::printOutput($data);
    }

    /**
     * Format output
     *
     * @param mixed $data Data
     *
     * @return mixed
     */
    protected function formatOutput($data)
    {
        $dumper = new \Symfony\Component\Yaml\Dumper;

        return $dumper->dump($data, 1);
    }

}
