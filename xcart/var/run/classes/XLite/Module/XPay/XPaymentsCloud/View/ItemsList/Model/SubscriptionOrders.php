<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\ItemsList\Model;

use XLite\Module\XPay\XPaymentsCloud\View\StickyPanel\SubscriptionOrdersPanel;

/**
 * Search order
 */
class SubscriptionOrders extends \XLite\View\ItemsList\Model\Order\Admin\Search
{
    /**
     * getXpaymentsSubscriptionId
     *
     * @return integer
     */
    protected function getXpaymentsSubscriptionId()
    {
        return \XLite\Core\Request::getInstance()->subscription_id;
    }

    /**
     * getXpaymentsSubscription
     *
     * @return \XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription
     */
    protected function getXpaymentsSubscription()
    {
        $subscriptionId = $this->getXpaymentsSubscriptionId();

        return \XLite\Core\Database::getRepo('\XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription')
            ->find($subscriptionId);
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = new \XLite\Core\CommonCell();
        // SEARCH_XPAYMENTS_SUBSCRIPTION defined in \XLite\Module\XPay\XPaymentsCloud\Model\Repo\Order
        $result->{\XLite\Model\Repo\Order::SEARCH_XPAYMENTS_SUBSCRIPTION} = $this->getXpaymentsSubscription();

        return $result;
    }

    /**
     * Mark list as non-selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return false;
    }

    /**
     * Mark list as non-removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return false;
    }

    /**
     * Get panel class
     *
     * @return string
     */
    protected function getPanelClass()
    {
        return SubscriptionOrdersPanel::class;
    }

}
