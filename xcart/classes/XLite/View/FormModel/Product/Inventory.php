<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Product;

class Inventory extends \XLite\View\FormModel\AFormModel
{
    /**
     * Do not render form_start and form_end in null returned
     *
     * @return string|null
     */
    protected function getTarget()
    {
        return 'product';
    }

    /**
     * @return string
     */
    protected function getAction()
    {
        return 'updateInventory';
    }

    /**
     * @return array
     */
    protected function getActionParams()
    {
        $params = ['page' => 'inventory'];

        $identity = $this->getDataObject()->default->identity;

        return $identity ? array_replace($params, ['product_id' => $identity]) : $params;
    }

    /**
     * @return array
     */
    protected function defineFields()
    {
        return [
            self::SECTION_DEFAULT => [
                'arrival_date'                      => [
                    'label'    => static::t('Arrival date'),
                    'type'     => 'XLite\View\FormModel\Type\DatepickerType',
                    'position' => 50,
                ],
                'inventory_tracking_status'         => [
                    'label'    => static::t('Inventory tracking for this product is'),
                    'type'     => 'XLite\View\FormModel\Type\SwitcherType',
                    'position' => 100,
                ],
                'quantity_origin' => [
                    'type' => 'Symfony\Component\Form\Extension\Core\Type\HiddenType',
                ],
                'quantity_in_stock'                 => [
                    'label'             => static::t('Quantity in stock'),
                    'type'              => 'XLite\View\FormModel\Type\InventoryQtyType',
                    'default'           => $this->getDataObject()->default->quantity_in_stock,
                    'inputmask_pattern' => [
                        'alias'      => 'integer',
                        'rightAlign' => false,
                    ],
                    'show_when'         => [
                        'default' => [
                            'inventory_tracking_status' => '1',
                        ],
                    ],
                    'position'          => 200,
                ],
                'low_stock_warning_on_product_page' => [
                    'label'     => static::t('Show low stock warning on product page'),
                    'type'      => 'XLite\View\FormModel\Type\SwitcherType',
                    'show_when' => [
                        'default' => [
                            'inventory_tracking_status' => '1',
                        ],
                    ],
                    'position'  => 300,
                ],
                'low_stock_admin_notification'      => [
                    'label'     => static::t('Notify administrator if the stock quantity of this product goes below a certain limit'),
                    'type'      => 'XLite\View\FormModel\Type\LowStockNotificationType',
                    'show_when' => [
                        'default' => [
                            'inventory_tracking_status' => '1',
                        ],
                    ],
                    'position'  => 400,
                ],
                'low_stock_limit'                   => [
                    'label'             => static::t('Low limit quantity'),
                    'type'              => 'XLite\View\FormModel\Type\PatternType',
                    'inputmask_pattern' => [
                        'alias'      => 'integer',
                        'rightAlign' => false,
                    ],
                    'show_when'         => [
                        'default' => [
                            'inventory_tracking_status' => '1',
                        ],
                    ],
                    'position'          => 500,
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    protected function getViewObjectGetterName()
    {
        return 'getInventoryFormModelObject';
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'product/inventory/style.css';

        return $list;
    }
}
