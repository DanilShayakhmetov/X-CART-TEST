<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\Attribute;

class AttributeTypeHidden extends \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    public static function getSchema($name, $options)
    {
        $position = isset($options['position']) ? $options['position'] : 0;
        $attribute = $options['attribute'];

        $attributeOptions = \XLite\Core\Database::getRepo('\XLite\Model\AttributeOption')
            ->findBy(['attribute' => $attribute], [ 'position' => 'asc' ]);

        $list = [];
        $list[0] = static::t('-- empty --');
        foreach ($attributeOptions as $option) {
            $list[$option->getId()] = $option->getName();
        }

        return [
            $name                => [
                'label'             => $attribute->getName(),
                'type'              => 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                'choices'           => array_flip($list),
                'placeholder'       => null,
                'position'          => $position,
            ],
        ];
    }

    public static function getData($name, $object)
    {
        return [
            $name . '_edit_mode' => 'add',
            $name                => 0,
        ];
    }

    public static function populateData($name, $object, $data)
    {
        $attributeId = null;
        if (preg_match('/^attribute_([0-9]+)/', $name, $m)) {
            $attributeId = $m[1];
        }

        if ($attributeId) {
            $attribute = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->find($attributeId);

            if ($attribute) {
                if (0 === $data->{$name}) {
                    static::removeAttributeValue($attribute, $object, $data->{$name});

                } else {
                    static::addAttributeValue($attribute, $object, $data->{$name});
                }
            }
        }
    }

    protected static function removeAttributeValue($attribute, $object, $data)
    {
        $attribute->setAttributeValue($object, ['value' => '']);
    }

    protected static function addAttributeValue($attribute, $object, $data)
    {
        if (is_array($data)) {
            $data = reset($data);
        }

        $option = \XLite\Core\Database::getRepo('XLite\Model\AttributeOption')->find($data);

        if ($option) {
            $attribute->setAttributeValue($object, ['value' => $option->getName()]);
        }
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return array
     */
    public static function getViewColumns($name, $options)
    {
        $attribute = $options['attribute'];

        $columnName = $attribute->getName();
        if ($group = $attribute->getAttributeGroup()) {
            $columnName = $columnName . ' (' . $group->getName() . ')';
        }

        return [
            $name => [
                'name'    => $columnName,
                'orderBy' => isset($options['position']) ? $options['position'] : 0,
            ],
        ];
    }

    /**
     * @param $name
     * @param $object
     *input-widget
     * @return array
     */
    public static function getViewValue($name, $object)
    {
        $result = '';
        $attributeId = null;
        if (preg_match('/^attribute_([0-9]+)/', $name, $m)) {
            $attributeId = $m[1];
        }

        if ($attributeId) {
            $attribute = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->find($attributeId);

            if ($attribute) {
                $attributeValue = \XLite\Core\Database::getRepo('XLite\Model\AttributeValue\AttributeValueHidden')->findOneBy(
                    [
                        'attribute' => $attribute,
                        'product'   => $object,
                    ]
                );

                if ($attributeValue) {
                    $result = $attributeValue->asString();
                }
            }
        }

        return $result;
    }
}
