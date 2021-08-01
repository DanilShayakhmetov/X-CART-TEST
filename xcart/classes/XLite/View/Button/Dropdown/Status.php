<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Dropdown;

use XLite\Core\PreloadedLabels\ProviderInterface;

/**
 * Status
 */
class Status extends \XLite\View\Button\Dropdown\ADropdown implements ProviderInterface
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'enable'  => [
                'params'   => [
                    'action'     => 'enable',
                    'label'      => 'Enable selected',
                    'style'      => 'always-enabled action action-enable link list-action',
                    'icon-style' => 'fa fa-power-off state-on iconfont',
                ],
                'position' => 100,
            ],
            'disable' => [
                'params'   => [
                    'action'     => 'disable',
                    'label'      => 'Disable selected',
                    'style'      => 'always-enabled action action-disable link list-action',
                    'icon-style' => 'fa fa-power-off state-off iconfont',
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
            'Enable selected' => static::t('Enable selected'),
            'Enable all'      => static::t('Enable all'),
            'Disable selected' => static::t('Disable selected'),
            'Disable all'      => static::t('Disable all'),
        ];
    }
}
