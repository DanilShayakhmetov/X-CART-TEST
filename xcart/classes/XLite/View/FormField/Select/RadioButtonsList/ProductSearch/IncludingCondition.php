<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\RadioButtonsList\ProductSearch;

use XLite\View\FormField\Select\RadioButtonsList\ARadioButtonsList;

class IncludingCondition extends ARadioButtonsList
{
    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'all'    => static::t('All words'),
            'any'    => static::t('Any word'),
            'phrase' => static::t('Exact phrase'),
        ];
    }

    /**
     * Get option attributes
     *
     * @param mixed $value Value
     * @param mixed $text  Text
     *
     * @return array
     */
    protected function getOptionAttributes($value, $text)
    {
        $attributes = parent::getOptionAttributes($value, $text);
        if ($value === 'all') {
            $attributes['class'] = ($attributes['class'] ?? '') . ' default';
        }

        return $attributes;
    }
}
