<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Core\Printer;

/**
 * XML printer
 */
class XML extends \XLite\Module\XC\RESTAPI\Core\Printer\Base\HTTP
{
    /**
     * Check - schema is own this request or not
     *
     * @return boolean
     */
    public static function isOwn()
    {
        return !empty($_SERVER['HTTP_ACCEPT'])
            && 'application/xml' == $_SERVER['HTTP_ACCEPT']
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
        header('Content-Type: application/xml;charset=utf-8');

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
        $result = '<' . '?xml version="1.1" encoding="UTF-8" ?' . '>';

        $result .= is_array($data)
            ? $this->convertToXMLArray($data)
            : $this->convertToXMLCell('body', $data);

        return $result;
    }

    /**
     * Convert to XML array
     *
     * @param array $data Data
     *
     * @return string
     */
    protected function convertToXMLArray(array $data)
    {
        $result = '';

        foreach ($data as $name => $value) {
            $result .= $this->convertToXMLCell($name, $value);
        }

        return $result;
    }

    /**
     * Convert to XML cell
     *
     * @param string $name  Cell name
     * @param mixed  $value Cell value
     *
     * @return string
     */
    protected function convertToXMLCell($name, $value)
    {
        $type = gettype($value);
        $result = '<' . $name . ' type="' . $type .'">';

        if (is_scalar($value)) {

            switch ($type) {
                case 'boolean':
                    $result .= $value ? 'true' : 'false';
                    break;

                case 'integer':
                case 'double':
                    $result .= $value;
                    break;

                case 'string':
                    $result .= htmlspecialchars($value, \ENT_XML1);
                    break;

                default:
            }

        } elseif (is_array($value)) {
            $result .= $this->convertToXMLArray($value);
        }

        return $result . '</' . $name . '>';
    }

}
