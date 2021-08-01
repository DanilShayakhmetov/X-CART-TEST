<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\FormField\Input\Text;


class AutoComplete extends \XLite\View\FormField\Input\Base\StringInput
{
    const PARAM_DICTIONARY = 'dictionary';

    public function getFieldType()
    {
        return static::FIELD_TYPE_TEXT;
    }

    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/XC/ThemeTweaker/form_field/input/text/autocomplete.js',
        ]);
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_DICTIONARY => new \XLite\Model\WidgetParam\TypeString('Data provider name', ''),
        ];
    }

    protected function getCommentedData()
    {
        return parent::getCommentedData() + [
                'data_source_url' => [
                    'target'     => 'autocomplete',
                    'dictionary' => $this->getParam(static::PARAM_DICTIONARY),
                ],
            ];
    }
}