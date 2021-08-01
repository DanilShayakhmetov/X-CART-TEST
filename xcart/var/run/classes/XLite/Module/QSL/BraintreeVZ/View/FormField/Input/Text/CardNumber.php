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
class CardNumber extends \XLite\Module\QSL\BraintreeVZ\View\FormField\Input\Text\AText 
{
    /**
     * Get DOM name for the braintree form field.
     *
     * @return string
     */
    protected function getDataBraintreeName()
    {
        return 'number';
    }

    /**
     * Get default placeholder
     *
     * @return string
     */
    protected function getDefaultPlaceholder()
    {
        return 'XXXX-XXXX-XXXX-XXXX';
    }

}
