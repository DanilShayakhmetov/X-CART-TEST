<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\ItemsList\Model;

use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription;

/**
 * Order info
 */
 class OrderItem extends \XLite\View\ItemsList\Model\OrderItemAbstract implements \XLite\Base\IDecorator
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XPay/XPaymentsCloud/subscription/style.css';

        return $list;
    }

    /**
     * Is next payment date available for current order
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return boolean
     */
    protected function isXpaymentsNextPaymentDateAvailable($item)
    {
        return $item->isXpaymentsNextPaymentDateAvailable();
    }

    /**
     * Is last payment failed for current subscription
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return boolean
     */
    protected function isXpaymentsLastPaymentFailed($item)
    {
        $subscription = $item->getXpaymentsSubscription();

        return $subscription
            && $subscription->getActualDate() > $subscription->getPlannedDate();
    }

    /**
     * Get next payment date
     *
     * @param Subscription $subscription Subscription
     *
     * @return integer
     */
    protected function getNextPaymentDate($subscription)
    {
        return $subscription->getPlannedDate();
    }

    /**
     * Get next attempt date
     *
     * @param Subscription $subscription Subscription
     *
     * @return integer
     */
    protected function getNextAattemptDate($subscription)
    {
        return $subscription->getActualDate();
    }

}
