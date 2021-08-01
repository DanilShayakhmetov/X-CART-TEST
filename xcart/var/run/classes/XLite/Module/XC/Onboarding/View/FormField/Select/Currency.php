<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\FormField\Select;

class Currency extends \XLite\View\FormField\Select\Currency
{
    protected function getOptionAttributes($value, $text)
    {
        $attributes = parent::getOptionAttributes($value, $text);

        /** @var \XLite\Model\Currency $currency */
        if ($currency = \XLite\Core\Database::getRepo('XLite\Model\Currency')->find($value)) {
            $attributes['data-prefix'] = $currency->getPrefix();
            $attributes['data-suffix'] = $currency->getSuffix();
        }

        return $attributes;
    }

    /**
     * Return some data for JS external scripts if it is needed.
     *
     * @return null|array
     */
    protected function getFormFieldJSData()
    {
        $parent = parent::getFormFieldJSData() ?: [];
        return array_merge($parent, [
            'currencies'        => $this->getCurrencyCodes(),
        ]);
    }

    /**
     * @return array
     */
    protected function getCurrencyCodes()
    {
        $list = [];
        /** @var \XLite\Model\Currency $currency */
        foreach (\XLite\Core\Database::getRepo('XLite\Model\Currency')->findAllSortedByName() as $currency) {
            $list[$currency->getCurrencyId()] = $currency->getCode();
        }
        asort($list);

        return $list;
    }
}