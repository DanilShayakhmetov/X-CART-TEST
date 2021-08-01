<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\View\FormField\Select;

/**
 * Expiration date month
 */
class Month extends \XLite\Module\QSL\BraintreeVZ\View\FormField\Select\ASelect
{
    /**
     * Get braintree "name" field. Not a name but still we need it.
     *
     * @return string
     */
    protected function getDataBraintreeName()
    {
        return 'expiration_month';
    }

    /**
     * Get minimum value for select.
     * 
     * @return int
     */
    protected function getMinValue()
    {   
        return 1;
    }

    /**
     * Get maximum value for select.
     *
     * @return int
     */
    protected function getMaxValue()
    {
        return 12;
    }
}
