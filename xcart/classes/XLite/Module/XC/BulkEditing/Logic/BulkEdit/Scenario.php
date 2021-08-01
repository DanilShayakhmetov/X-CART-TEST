<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\Logic\BulkEdit;

class Scenario
{
    public static $searchCndSessionCell = 'bulkEditingSearchSessionCell';

    /**
     * @var array
     */
    protected static $scenarioData;

    /**
     * @param string $scenario
     *
     * @return array|null
     */
    public static function getScenarioData($scenario)
    {
        $scenarioSpecificMethod = 'defineScenarioFor' . ucfirst(\Includes\Utils\Converter::convertToCamelCase($scenario));
        if (method_exists(get_called_class(), $scenarioSpecificMethod)) {
            return static::{$scenarioSpecificMethod}();
        }

        if (null === self::$scenarioData) {
            self::$scenarioData = static::defineScenario();
        }

        return isset(self::$scenarioData[$scenario]) ? self::$scenarioData[$scenario] : null;
    }

    /**
     * @return array|null
     */
    public static function getScenarios()
    {
        if (null === self::$scenarioData) {
            self::$scenarioData = static::defineScenario();
        }

        return self::$scenarioData;
    }

    /**
     * @param string $scenario
     *
     * @return string|null
     */
    public static function getScenarioDTO($scenario)
    {
        return static::getScenarioDataField($scenario, 'DTO');
    }

    /**
     * @param string $scenario
     *
     * @return string|null
     */
    public static function getScenarioFormModel($scenario)
    {
        return static::getScenarioDataField($scenario, 'formModel');
    }

    /**
     * @param string $scenario
     *
     * @return string|null
     */
    public static function getScenarioView($scenario)
    {
        return static::getScenarioDataField($scenario, 'view');
    }

    /**
     * @param string $scenario
     *
     * @return string|null
     */
    public static function getScenarioStep($scenario)
    {
        return static::getScenarioDataField($scenario, 'step');
    }

    /**
     * @param string $scenario
     *
     * @return array
     */
    public static function getScenarioFields($scenario)
    {
        return static::getScenarioDataField($scenario, 'fields');
    }

    /**
     * @param string $scenario
     *
     * @return string|null
     */
    public static function getScenarioSections($scenario)
    {
        return static::getScenarioDataField($scenario, 'sections');
    }

    /**
     * @param string $scenario
     * @param string $field
     *
     * @return null|string
     */
    protected static function getScenarioDataField($scenario, $field)
    {
        $scenarioData = static::getScenarioData($scenario);

        return $scenarioData && isset($scenarioData[$field]) ? $scenarioData[$field] : null;
    }

    /**
     * @return array
     */
    protected static function defineScenario()
    {
        return [
            'product_categories'           => [
                'title'     => \XLite\Core\Translation::getInstance()->translate('Categories'),
                'formModel' => 'XLite\Module\XC\BulkEditing\View\FormModel\Product\Categories',
                'view'      => 'XLite\Module\XC\BulkEditing\View\ItemsList\BulkEdit\Product\Category',
                'DTO'       => 'XLite\Module\XC\BulkEditing\Model\DTO\Product\Categories',
                'step'      => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Step\Product',
                'fields'    => [
                    'default' => [
                        'categories' => [
                            'class'   => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\Category',
                            'options' => [
                                'position' => 100,
                            ],
                        ],
                    ],
                ],
            ],
            'product_inventory'            => [
                'title'     => \XLite\Core\Translation::getInstance()->translate('Inventory'),
                'formModel' => 'XLite\Module\XC\BulkEditing\View\FormModel\Product\Inventory',
                'view'      => 'XLite\Module\XC\BulkEditing\View\ItemsList\BulkEdit\Product\Inventory',
                'DTO'       => 'XLite\Module\XC\BulkEditing\Model\DTO\Product\Inventory',
                'step'      => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Step\Product',
                'fields'    => [
                    'default' => [
                        'inventory_tracking_status'         => [
                            'class'   => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\InventoryTrackingStatus',
                            'options' => [
                                'position' => 100,
                            ],
                        ],
                        'quantity_in_stock'                 => [
                            'class'   => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\QuantityInStock',
                            'options' => [
                                'position' => 200,
                            ],
                        ],
                        'low_stock_warning_on_product_page' => [
                            'class'   => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\LowStockWarningOnProductPage',
                            'options' => [
                                'position' => 300,
                            ],
                        ],
                        'low_stock_admin_notification'      => [
                            'class'   => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\LowStockAdminNotification',
                            'options' => [
                                'position' => 400,
                            ],
                        ],
                        'low_stock_limit'                   => [
                            'class'   => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\LowStockLimit',
                            'options' => [
                                'position' => 500,
                            ],
                        ],
                    ],
                ],
            ],
            'product_price_and_membership' => [
                'title'     => \XLite\Core\Translation::getInstance()->translate('Price and membership'),
                'formModel' => 'XLite\Module\XC\BulkEditing\View\FormModel\Product\PriceAndMembership',
                'view'      => 'XLite\Module\XC\BulkEditing\View\ItemsList\BulkEdit\Product\PriceAndMembership',
                'DTO'       => 'XLite\Module\XC\BulkEditing\Model\DTO\Product\PriceAndMembership',
                'step'      => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Step\Product',
                'fields'    => [
                    'default' => [
                        'price'       => [
                            'class'   => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\Price',
                            'options' => [
                                'position' => 100,
                            ],
                        ],
                        'memberships' => [
                            'class'   => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\Membership',
                            'options' => [
                                'position' => 200,
                            ],
                        ],
                        'product_class' => [
                            'class'   => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\TaxClass',
                            'options' => [
                                'position' => 300,
                            ],
                        ],
                    ],
                ],
            ],
            'product_shipping_info'        => [
                'title'     => \XLite\Core\Translation::getInstance()->translate('Shipping info'),
                'formModel' => 'XLite\Module\XC\BulkEditing\View\FormModel\Product\ShippingInfo',
                'view'      => 'XLite\Module\XC\BulkEditing\View\ItemsList\BulkEdit\Product\ShippingInfo',
                'DTO'       => 'XLite\Module\XC\BulkEditing\Model\DTO\Product\ShippingInfo',
                'step'      => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Step\Product',
                'fields'    => [
                    'default' => [
                        'weight'            => [
                            'class'   => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\Weight',
                            'options' => [
                                'position' => 100,
                            ],
                        ],
                        'requires_shipping' => [
                            'class'   => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\RequiresShipping',
                            'options' => [
                                'position' => 200,
                            ],
                        ],
                        'separate_box'      => [
                            'class'   => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\SeparateBox',
                            'options' => [
                                'position' => 300,
                            ],
                        ],
                        'length'            => [
                            'class'   => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\Length',
                            'options' => [
                                'position' => 400,
                            ],
                        ],
                        'width'             => [
                            'class'   => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\Width',
                            'options' => [
                                'position' => 500,
                            ],
                        ],
                        'height'            => [
                            'class'   => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\Height',
                            'options' => [
                                'position' => 600,
                            ],
                        ],
                        'max_items_in_box'  => [
                            'class'   => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\MaximumItemsInBox',
                            'options' => [
                                'position' => 700,
                            ],
                        ],
                    ],
                ],
            ],
            'product_hidden_attributes'    => [
                'title'     => \XLite\Core\Translation::getInstance()->translate('Hidden attributes'),
                'formModel' => 'XLite\Module\XC\BulkEditing\View\FormModel\Product\Attribute\HiddenAttribute',
                'view'      => 'XLite\Module\XC\BulkEditing\View\ItemsList\BulkEdit\Product\Attribute\HiddenAttribute',
                'DTO'       => 'XLite\Module\XC\BulkEditing\Model\DTO\Product\Attribute\HiddenAttribute',
                'step'      => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Step\Product',
                'fields'    => [],
            ],
        ];
    }

    protected static function defineScenarioForProductHiddenAttributes()
    {
        $fields = [];
        $sections = [];

        $attributes = static::getHiddenAttributesList();

        foreach ($attributes as $attribute) {
            $group = $attribute->getAttributeGroup();

            if (!$group) {
                $groupName = 'default';
            } else {
                $groupName = 'group_' . $group->getId();
                $sections[$groupName] = [
                    'label' => $group->getName(),
                ];
            }

            if (!isset($fields[$groupName])) {
                $fields[$groupName] = [];
            }

            $name = 'attribute_' . $attribute->getId();
            $options = [
                'position' => $attribute->getPosition(),
                'attribute' => $attribute,
            ];

            $fields[$groupName][$name] = [
                'class' => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\Attribute\AttributeTypeHidden',
                'options' => $options,
            ];
        }

        $scenarioData = [
            'title'     => \XLite\Core\Translation::getInstance()->translate('Hidden attributes'),
            'formModel' => 'XLite\Module\XC\BulkEditing\View\FormModel\Product\Attribute\HiddenAttribute',
            'view'      => 'XLite\Module\XC\BulkEditing\View\ItemsList\BulkEdit\Product\Attribute\HiddenAttribute',
            'DTO'       => 'XLite\Module\XC\BulkEditing\Model\DTO\Product\Attribute\HiddenAttribute',
            'step'      => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Step\Product',
            'fields'    => $fields,
            'sections'  => $sections,
        ];

        return $scenarioData;
    }

    protected static $hiddenAttributes;

    /**
     * @return \XLite\Model\Attribute[]
     */
    protected static function getHiddenAttributesList()
    {
        if (!isset(static::$hiddenAttributes)) {
            $cnd = new \XLite\Core\CommonCell();

            $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT} = null;
            $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT_CLASS} = null;
            $cnd->{\XLite\Model\Repo\Attribute::SEARCH_TYPE} = \XLite\Model\Attribute::TYPE_HIDDEN;

            static::$hiddenAttributes = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->search($cnd);
        }

        return static::$hiddenAttributes;
    }
}
