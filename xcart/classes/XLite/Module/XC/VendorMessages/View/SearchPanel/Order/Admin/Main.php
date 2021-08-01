<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\SearchPanel\Order\Admin;

/**
 * Main admin orders list search panel
 */
class Main extends \XLite\View\SearchPanel\Order\Admin\Main implements \XLite\Base\IDecorator
{
    /**
     * Define hidden conditions
     *
     * @return array
     */
    protected function defineHiddenConditions()
    {
        return parent::defineHiddenConditions() + [
            'messages' => [
                static::CONDITION_CLASS                       => 'XLite\Module\XC\VendorMessages\View\FormField\Select\OrderMessages',
                \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('Messages'),
            ],
        ];
    }
}
