<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product;

class TaxClass extends \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    public static function getSchema($name, $options)
    {
        $position = isset($options['position']) ? $options['position'] : 0;

        $list = [];
        foreach (\XLite\Core\Database::getRepo('\XLite\Model\TaxClass')->findAll() as $e) {
            $list[$e->getId()] = $e->getName();
        }

        return [
            $name                => [
                'label'             => static::t('Tax Class'),
                'type'              => 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                'choices'           => array_flip($list),
                'placeholder'       => static::t('Default tax class'),
                'position'          => $position,
            ],
        ];
    }

    public static function getData($name, $object)
    {
        return [
            $name => '0',
        ];
    }

    public static function populateData($name, $object, $data)
    {
        /** @var \XLite\Model\Product $object */
        $class = $data->{$name}
            ? \XLite\Core\Database::getRepo('XLite\Model\TaxClass')->find($data->{$name})
            : null;

        $object->setTaxClass($class);
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
                'name'    => static::t('Tax Class'),
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
        /** @var \XLite\Model\Product $object */
        return $object->getTaxClass()
            ? $object->getTaxClass()->getName()
            : static::t('Default tax class');
    }
}
