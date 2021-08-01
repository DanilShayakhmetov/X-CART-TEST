<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\View\FormField\Input\Text;

/**
 * Cardholder name input 
 *
 */
class CardName extends \XLite\Module\QSL\BraintreeVZ\View\FormField\Input\Text\AText 
{
    /**
     * Get DOM name for the braintree form field.
     *
     * @return string
     */
    protected function getDataBraintreeName()
    {
        return 'cardholder_name';
    }
}
