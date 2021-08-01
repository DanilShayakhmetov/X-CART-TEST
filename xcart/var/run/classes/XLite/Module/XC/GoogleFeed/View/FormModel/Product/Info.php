<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\View\FormModel\Product;


/**
 * Product form model
 */
 class Info extends \XLite\Module\XC\Onboarding\View\FormModel\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = parent::defineFields();
        $schema['marketing']['googleFeedEnabled'] = [
            'label'      => static::t('Add to Google product feed'),
            'type'       => 'XLite\View\FormModel\Type\SwitcherType',
            'position'   => 700,
            'show_when' => [
                static::SECTION_DEFAULT => [
                    'available_for_sale' => '1',
                ],
            ],
        ];

        return $schema;
    }
}