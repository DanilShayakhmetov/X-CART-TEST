<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Select\Select2;


class Countries extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return '\XLite\View\FormField\Select\Select2\Countries';
    }

    /**
     * Check - field is editable or not
     *
     * @return boolean
     */
    protected function hasSeparateView()
    {
        return false;
    }

    /**
     * Preprocess value before save
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    protected function preprocessValueBeforeSave($value)
    {
        return array_map(function ($code) {
            return \XLite\Core\Database::getRepo('XLite\Model\Country')->find($code);
        }, (array)$value);
    }
}