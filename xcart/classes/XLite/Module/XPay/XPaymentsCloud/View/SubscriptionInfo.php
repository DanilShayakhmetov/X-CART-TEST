<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View;

use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription;

/**
 * Subscription info view
 *
 */
class SubscriptionInfo extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XPay/XPaymentsCloud/subscription_info/body.twig';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XPay/XPaymentsCloud/subscription_info/style.css';

        return $list;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getXpaymentsSubscription();
    }

    /**
     * Return subscription entity
     *
     * @return \XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription
     */
    protected function getXpaymentsSubscription()
    {
        $subscriptionId = isset(\XLite\Core\Request::getInstance()->subscription_id)
            ? \XLite\Core\Request::getInstance()->subscription_id
            : 0;

        return \XLite\Core\Database::getRepo('\XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription')
            ->find($subscriptionId);
    }

    /**
     * Get formatted date string
     *
     * @param integer $time Time
     *
     * @return string
     */
    protected function getTime($time)
    {
        return \XLite\Core\Converter::getInstance()->formatDate(intval($time));
    }

    /**
     * Get formatted subscription status
     *
     * @return string
     */
    protected function getFormattedStatus()
    {
        $statuses = [
            Subscription::STATUS_NOT_STARTED => static::t('Not started'),
            Subscription::STATUS_RESTARTED   => static::t('Restarted'),
            Subscription::STATUS_ACTIVE      => static::t('Active'),
            Subscription::STATUS_STOPPED     => static::t('Stopped'),
            Subscription::STATUS_FAILED      => static::t('Failed'),
            Subscription::STATUS_FINISHED    => static::t('Finished'),
        ];

        return $statuses[$this->getXpaymentsSubscription()->getStatus()];
    }

    /**
     * Details array for widget
     *
     * @return array
     */
    protected function getDetails()
    {
        $details = [
            'Subscription status' => $this->getFormattedStatus(),
            'Product name'        => $this->getXpaymentsSubscription()->getProductName(),
            'Start date'          => $this->getTime($this->getXpaymentsSubscription()->getStartDate()),
            'Next payment date'   => $this->getTime($this->getXpaymentsSubscription()->getPlannedDate()),
            'Successful payments' => $this->getXpaymentsSubscription()->getSuccessfulAttempts(),
            'Calculate shipping'  => ($this->getXpaymentsSubscription()->getCalculateShipping())
                ? 'Yes'
                : 'No',
        ];

        if ($this->getXpaymentsSubscription()->getFailedAttempts()) {
            $details['Failed attempts for last payment'] = $this->getXpaymentsSubscription()->getFailedAttempts();
        }

        if ($this->getXpaymentsSubscription()->getPeriods()) {
            $details['Number of remaining payments'] = $this->getXpaymentsSubscription()->getRemainingPayments();
        }

        return $details;
    }

}
