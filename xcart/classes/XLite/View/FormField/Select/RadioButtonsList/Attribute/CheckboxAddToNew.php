<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\RadioButtonsList\Attribute;

/**
 * Checkbox pre-defined value radio buttons list
 */
class CheckboxAddToNew extends \XLite\View\FormField\Select\RadioButtonsList\ARadioButtonsList
{
    const NO_VALUE_OPTION = 'no_value';

    /**
     * Get default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            static::NO_VALUE_OPTION  => static::t('No pre-defined value'),
            '1'   => static::t('Yes'),
            '0'   => static::t('No'),
        );
    }

    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        $value = $this->getParam(self::PARAM_VALUE);
        if (is_array($value)) {
            $value = count($value) === 2 ? static::NO_VALUE_OPTION : array_shift($value);
        }
        $result = $value;
        $options = $this->getOptions();
        if (!(isset($value) && isset($options[$value]))) {
            $value = array_keys($options);
            $result = array_shift($value);
        }

        return $result;
    }
}
