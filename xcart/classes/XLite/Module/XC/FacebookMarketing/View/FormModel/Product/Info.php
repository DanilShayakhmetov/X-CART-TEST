<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\View\FormModel\Product;


/**
 * Product form model
 */
class Info extends \XLite\View\FormModel\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = parent::defineFields();
        $schema['marketing']['facebookMarketingEnabled'] = [
            'label'      => static::t('Facebook product feed enabled'),
            'type'       => 'XLite\View\FormModel\Type\SwitcherType',
            'position'   => 650,
            'show_when' => [
                static::SECTION_DEFAULT => [
                    'available_for_sale' => '1',
                ],
            ],
        ];

        return $schema;
    }
}