<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\FormField\Select;

use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription;
use XLite\Module\XPay\XPaymentsCloud\Model\Repo\Subscription\Subscription as SubscriptionRepo;

/**
 * Subscription status selector
 */
class SubscriptionStatus extends \XLite\View\FormField\Select\Regular
{
    /**
     * Widget param names
     */
    const PARAM_DISPLAY_SEARCH_STATUSES = 'displaySearchStatuses';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_DISPLAY_SEARCH_STATUSES => new \XLite\Model\WidgetParam\TypeBool(
                static::t('Display search related statuses'),
                false
            ),
        ];
    }

    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        $list = parent::getOptions();

        if ($this->getParam(static::PARAM_DISPLAY_SEARCH_STATUSES)) {
            $list = [
                    SubscriptionRepo::STATUS_ANY => static::t('Any status'),
                ]
                + $list
                + [
                    SubscriptionRepo::STATUS_EXPIRED       => static::t('Expired'),
                    SubscriptionRepo::STATUS_ACTIVE_FAILED => static::t('Active, with failed transaction'),
                ];
        }

        return $list;
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            Subscription::STATUS_ACTIVE      => static::t('Active'),
            Subscription::STATUS_RESTARTED   => static::t('Restarted'),
            Subscription::STATUS_NOT_STARTED => static::t('Not started'),
            Subscription::STATUS_STOPPED     => static::t('Stopped'),
            Subscription::STATUS_FAILED      => static::t('Failed'),
            Subscription::STATUS_FINISHED    => static::t('Finished'),
        ];
    }

}
