<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Logic\BulkEdit;

/**
 * @Decorator\Depend ("XC\BulkEditing")
 */
class Scenario extends \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected static function defineScenario()
    {
        $result = parent::defineScenario();

        $result['coupons'] = [
            'title'     => \XLite\Core\Translation::getInstance()->translate('Coupons'),
            'formModel' => 'XLite\Module\CDev\Coupons\View\FormModel\BulkEdit\Product\Coupons',
            'view'      => 'XLite\Module\CDev\Coupons\View\ItemsList\BulkEdit\Product\Coupons',
            'DTO'       => 'XLite\Module\CDev\Coupons\Model\DTO\BulkEdit\Product\Coupons',
            'step'      => 'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Step\Product',
            'fields'    => [
                'default' => [
                    'coupons' => [
                        'class'   => 'XLite\Module\CDev\Coupons\Logic\BulkEdit\Field\Product\Coupons',
                        'options' => [
                            'position' => 100,
                        ],
                    ],
                ],
            ],
        ];

        return $result;
    }
}
