<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\FormField;

/**
 * Price or percent
 */
class PriceRangeLink extends \XLite\View\FormField\Link
{
    const PARAM_MIN_PRICE = 'min_price';
    const PARAM_MAX_PRICE = 'max_price';
    const PARAM_CURRENCY = 'currency';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_MIN_PRICE => new \XLite\Model\WidgetParam\TypeFloat('Min price', 0),
            self::PARAM_MAX_PRICE => new \XLite\Model\WidgetParam\TypeFloat('Max price', 0),
            self::PARAM_CURRENCY => new \XLite\Model\WidgetParam\TypeObject(
                'Currency',
                \XLite::getInstance()->getCurrency(),
                false,
                'XLite\Model\Currency'
            ),
        );
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'modules/XC/ProductVariants/form_field/price_range_link/body.twig';
    }

    /**
     * getAttributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $attributes = parent::getAttributes();

        $attributes['href'] = $this->getHref();

        return $attributes;
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return '';
    }

    /**
     * @return mixed
     */
    protected function getMinPrice()
    {
        return $this->getParam(self::PARAM_MIN_PRICE);
    }

    /**
     * @return mixed
     */
    protected function getMaxPrice()
    {
        return $this->getParam(self::PARAM_MAX_PRICE);
    }

    /**
     * Get currency
     *
     * @return \XLite\Model\Currency
     */
    public function getCurrency()
    {
        return $this->getParam(static::PARAM_CURRENCY);
    }
}
