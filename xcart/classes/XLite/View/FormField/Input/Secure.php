<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input;


/**
 * Secure input
 */
class Secure extends \XLite\View\FormField\Input\Base\StringInput
{
    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_PASSWORD;
    }

    /**
     * Return array of password difficulty labels in following format.
     *
     * 'label' => 'translation'
     *
     * @return mixed
     */
    protected function getPasswordDifficultyLabels()
    {
        return [
            'Weak password' => static::t('Weak password'),
            'Good password' => static::t('Good password'),
            'Strong password' => static::t('Strong password'),
        ];
    }
}
