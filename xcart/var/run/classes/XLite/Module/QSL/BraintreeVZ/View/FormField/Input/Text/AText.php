<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\View\FormField\Input\Text;

/**
 * Card number input 
 *
 */
abstract class AText extends \XLite\View\FormField\Input\Text
{
    /**
     * Widget param names
     */
    const ATTR_DATA_BRAINTREE_NAME = 'data-braintree-name';

    /**
     * Get DOM name for the braintree form field.
     */
    abstract protected function getDataBraintreeName();

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
}
