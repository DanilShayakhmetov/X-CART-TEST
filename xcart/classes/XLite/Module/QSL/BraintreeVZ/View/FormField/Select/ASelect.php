<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\View\FormField\Select;

/**
 * Expiration date 
 */
abstract class ASelect extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Widget param names
     */
    const ATTR_DATA_BRAINTREE_NAME = 'data-braintree-name';

    /**
     * Get braintree "name" field. Not a name but still we need it.
     */
    abstract protected function getDataBraintreeName();

    /**
     * Get minimum value for select.
     */
    abstract protected function getMinValue();

    /**
     * Get minimum value for select.
     */
    abstract protected function getMaxValue();

    /**
     * Define array of attributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $attrs = parent::getAttributes();

        $attrs[static::ATTR_DATA_BRAINTREE_NAME] = $this->getDataBraintreeName();

        return $attrs;
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $options = array('' => '');

        for ($i = $this->getMinValue(); $i <= $this->getMaxValue(); $i++) {
            $options[strval($i)] = $i;
        }

        return $options;
    }
}
