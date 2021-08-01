<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\FormField\Input;

/**
 * Price or percent
 */
class Sale extends \XLite\View\FormField\Input\PriceOrPercent
{
    /**
     * Register CSS class to use for wrapper block of input field.
     * It is usable to make unique changes of the field.
     *
     * @return string
     */
    public function getWrapperClass()
    {
        return parent::getWrapperClass() . ' no-sanitize';
    }

    /**
     * Returns Price widget params
     *
     * @return array
     */
    protected function getPriceWidgetParams()
    {
        $params = parent::getPriceWidgetParams();

        $params[static::PARAM_PLACEHOLDER] = $this->getParam(static::PARAM_PLACEHOLDER);

        return $params;
    }

    /**
     * Returns Price widget class
     *
     * @return string
     */
    protected function getPriceWidgetClass()
    {
        return 'XLite\Module\XC\ProductVariants\View\FormField\Input\Text\FloatInput';
    }

    /**
     * Returns default Price value
     *
     * @return mixed
     */
    protected function getDefaultPriceValue()
    {
        return '';
    }
}
