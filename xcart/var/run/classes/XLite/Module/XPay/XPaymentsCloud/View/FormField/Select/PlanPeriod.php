<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\FormField\Select;

use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Plan;

/**
 * Plan period selector
 */
class PlanPeriod extends \XLite\View\FormField\Select\ASelect
{
    const PARAM_PLAN_TYPE = 'planType';

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            Plan::TYPE_EACH  => [
                Plan::PERIOD_WEEK  => static::t('xps.week'),
                Plan::PERIOD_MONTH => static::t('xps.month'),
                Plan::PERIOD_YEAR  => static::t('xps.year'),
            ],
            Plan::TYPE_EVERY => [
                Plan::PERIOD_DAY   => static::t('xps.days'),
                Plan::PERIOD_WEEK  => static::t('xps.weeks'),
                Plan::PERIOD_MONTH => static::t('xps.months'),
                Plan::PERIOD_YEAR  => static::t('xps.years'),
            ],
        ];
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
            static::PARAM_PLAN_TYPE => new \XLite\Model\WidgetParam\TypeString('Plan type', Plan::TYPE_EACH),
        ];
    }

    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = parent::getOptions();

        return $options[$this->getParam(static::PARAM_PLAN_TYPE)];
    }

}
