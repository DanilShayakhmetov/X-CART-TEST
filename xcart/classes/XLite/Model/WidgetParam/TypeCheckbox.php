<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\WidgetParam;

/**
 * ____description____
 */
class TypeCheckbox extends \XLite\Model\WidgetParam\AWidgetParam
{
    /**
     * Param type
     *
     * @var string
     */
    protected $type = 'checkbox';

    /**
     * Return list of conditions to check
     *
     * @param mixed $value Value to validate
     *
     * @return void
     */
    protected function getValidationSchema($value)
    {
        return array(
            array(
                static::ATTR_CONDITION => !in_array($value, array(0, 1)),
                static::ATTR_MESSAGE   => ' only available values are (checked,unchecked)',
            ),
        );
    }
}
