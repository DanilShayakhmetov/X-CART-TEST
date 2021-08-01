<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View;

use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription;

/**
 * Account pin codes page order block
 *
 *
 */
class CustomerSubscription extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    const PARAM_SUBSCRIPTION = 'xpaymentsSubscription';

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XPay/XPaymentsCloud/subscription/style.css';
        $list[] = 'modules/XPay/XPaymentsCloud/checkout/widget.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XPay/XPaymentsCloud/subscription/script.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XPay/XPaymentsCloud/subscription/subscription.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_SUBSCRIPTION => new \XLite\Model\WidgetParam\TypeObject(
                'Subscription',
                null,
                false,
                '\\XLite\\Module\\XPay\\XPaymentsCloud\\Model\\Subscription\\Subscription'
            ),
        ];
    }

    /**
     * Get subscription status class
     *
     * @param Subscription $subscription Subscription
     *
     * @return string
     */
    protected function getStatusClass($subscription)
    {
        return 'status-' . $subscription->getStatus();
    }

    /**
     * getStatusName
     *
     * @param Subscription $subscription Subscription
     *
     * @return string
     */
    protected function getStatusName($subscription)
    {
        $statuses = [
            Subscription::STATUS_NOT_STARTED => static::t('Not started'),
            Subscription::STATUS_RESTARTED   => static::t('Restarted'),
            Subscription::STATUS_ACTIVE      => static::t('Active'),
            Subscription::STATUS_STOPPED     => static::t('Stopped'),
            Subscription::STATUS_FAILED      => static::t('Failed'),
            Subscription::STATUS_FINISHED    => static::t('Finished'),
        ];

        return $statuses[$subscription->getStatus()];
    }

    /**
     * isXpaymentsLastPaymentFailed
     *
     * @param Subscription $subscription Subscription
     *
     * @return boolean
     */
    protected function isXpaymentsLastPaymentFailed($subscription)
    {
        return Subscription::STATUS_FAILED !== $subscription->getStatus()
            && $subscription->getActualDate() > $subscription->getPlannedDate();
    }

    /**
     * isXpaymentsLastPaymentExpired
     *
     * @param Subscription $subscription Subscription
     *
     * @return boolean
     */
    protected function isXpaymentsLastPaymentExpired($subscription)
    {
        return $subscription->getActualDate() < \XLite\Module\XPay\XPaymentsCloud\Core\Converter::now();
    }

    /**
     * isXpaymentsNextDateVisible
     *
     * @param Subscription $subscription Subscription
     *
     * @return boolean
     */
    protected function isXpaymentsNextDateVisible($subscription)
    {
        return Subscription::STATUS_NOT_STARTED !== $subscription->getStatus()
            && Subscription::STATUS_FINISHED !== $subscription->getStatus()
            && Subscription::STATUS_STOPPED !== $subscription->getStatus()
            && Subscription::STATUS_FAILED !== $subscription->getStatus();
    }

    /**
     * Define line class as list of names
     *
     * @param Subscription $subscription Subscription
     *
     * @return string
     */
    protected function getXpaymentsLineClass($subscription)
    {
        $class = '';

        if ($this->isXpaymentsLastPaymentFailed($subscription)) {
            $class = 'last-payment-failed';
        }

        if ($this->isXpaymentsLastPaymentExpired($subscription)) {
            $class = 'last-payment-expired';
        }

        return $class;
    }

}
