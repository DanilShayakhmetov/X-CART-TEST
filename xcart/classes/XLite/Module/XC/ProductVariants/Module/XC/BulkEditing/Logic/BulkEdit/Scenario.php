<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Module\XC\BulkEditing\Logic\BulkEdit;


/**
 * Scenario
 *
 * @Decorator\Depend("XC\BulkEditing")
 */
class Scenario extends \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario implements \XLite\Base\IDecorator
{
    protected static function defineScenario()
    {
        return array_merge_recursive(parent::defineScenario(), [
            'product_inventory' => [
                'fields' => [
                    'default' => [
                        'clear_variants_inventory' => [
                            'class'   => 'XLite\Module\XC\ProductVariants\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\VariantsTrackingStatus',
                            'options' => [
                                'position' => 150,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}