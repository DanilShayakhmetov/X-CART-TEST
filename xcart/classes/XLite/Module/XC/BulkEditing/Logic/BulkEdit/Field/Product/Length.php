<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product;

class Length extends \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    public static function getSchema($name, $options)
    {
        return [
            $name => [
                'label'             => static::t('Length'),
                'type'              => 'XLite\View\FormModel\Type\SymbolType',
                'symbol'            => \XLite\Core\Config::getInstance()->Units->dim_symbol,
                'inputmask_pattern' => [
                    'alias'      => 'decimal',
                    'rightAlign' => false,
                ],
                'position'          => isset($options['position']) ? $options['position'] : 0,
            ],
        ];
    }

    public static function getData($name, $object)
    {
        return [
            $name => 0,
        ];
    }

    public static function populateData($name, $object, $data)
    {
        $object->setBoxLength($data->{$name});
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return array
     */
    public static function getViewColumns($name, $options)
    {
        return [
            $name => [
                'name'    => static::t('Length'),
                'orderBy' => isset($options['position']) ? $options['position'] : 0,
            ],
        ];
    }

    /**
     * @param $name
     * @param $object
     *
     * @return array
     */
    public static function getViewValue($name, $object)
    {
        return $object->getShippable() && $object->getUseSeparateBox()
            ? $object->getBoxLength()
            : '';
    }
}
