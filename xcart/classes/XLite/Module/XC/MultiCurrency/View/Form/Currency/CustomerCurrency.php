<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\Form\Currency;

use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;

/**
 * Currency management page form
 */
class CustomerCurrency extends \XLite\View\Form\AForm
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'change_currency';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update';
    }

    protected function getCommonFormParams()
    {
        $params = parent::getCommonFormParams();
        $multiCurrency = MultiCurrency::getInstance();

        if ($multiCurrency->getEnabledCountriesCount() === 1) {
            $params['country_code'] = $multiCurrency->getSelectedCountry()->getCode();
        }

        return $params;
    }
}