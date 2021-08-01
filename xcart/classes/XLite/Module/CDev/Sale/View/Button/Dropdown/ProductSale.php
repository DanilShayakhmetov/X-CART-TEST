<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\Button\Dropdown;

use XLite\Core\PreloadedLabels\ProviderInterface;

/**
 * Product status
 */
class ProductSale extends \XLite\View\Button\Dropdown\ADropdown implements ProviderInterface
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'sale'   => [
                'class'    => 'XLite\Module\CDev\Sale\View\SaleSelectedButton',
                'params'   => [
                    'label'      => 'Put up for sale',
                    'style'      => 'always-enabled action link list-action action-enable-sale',
                    'icon-style' => 'fa fa-percent state-on',
                ],
                'position' => 100,
            ],
            'cancel' => [
                'params'   => [
                    'action'     => 'sale_cancel_sale',
                    'label'      => 'Cancel sale',
                    'style'      => 'always-enabled action link list-action action-disable-sale',
                    'icon-style' => 'fa fa-percent state-off',
                ],
                'position' => 200,
            ],
        ];
    }

    /**
     * getDefaultStyle
     *
     * @return string
     */
    protected function getDefaultButtonClass()
    {
        return parent::getDefaultButtonClass() . ' contains-translation-data';
    }

    /**
     * Array of labels in following format.
     *
     * 'label' => 'translation'
     *
     * @return mixed
     */
    public function getPreloadedLanguageLabels()
    {
        return [
            'Put up for sale'     => static::t('Put up for sale'),
            'Put all for sale'    => static::t('Put all for sale'),
            'Cancel sale'         => static::t('Cancel sale'),
            'Cancel sale for all' => static::t('Cancel sale for all'),
        ];
    }
}
