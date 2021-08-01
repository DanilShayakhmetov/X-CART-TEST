<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Currencies list
 */
class Currency extends \XLite\View\FormField\Select\Regular
{
    /**
     * Additional widget param
     */
    const PARAM_USE_CODE_AS_KEY = 'useCodeAsKey';

    /**
     * Get options list
     *
     * @return array
     */
    protected function getOptions()
    {
        $list = [];

        foreach (\XLite\Core\Database::getRepo('XLite\Model\Currency')->findAllSortedByName() as $currency) {
            $key = $this->getParam(self::PARAM_USE_CODE_AS_KEY)
                ? $currency->getCode()
                : $currency->getCurrencyId();

            $list[$key] = sprintf('%s - %s', $currency->getCode(), $currency->getName());
        }

        return $list;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_USE_CODE_AS_KEY => new \XLite\Model\WidgetParam\TypeBool('Use currency codes as keys', false),
        ];
    }
}
