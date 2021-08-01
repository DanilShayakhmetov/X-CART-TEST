<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\ItemsList\Model;

use \XLite\Module\XPay\XPaymentsCloud\Model\Repo\Subscription\Subscription as SubscriptionRepo;

/**
 * Subscriptions items list
 */
class ProfileSubscription extends \XLite\Module\XPay\XPaymentsCloud\View\ItemsList\Model\Subscription
{
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $list = parent::defineColumns();
        $list['product'][static::COLUMN_MAIN] = true;
        unset($list['profile']);

        return $list;
    }

    /**
     * Get search condition
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = new \XLite\Core\CommonCell();
        // magic (see \XLite\Module\XPay\XPaymentsCloud\Controller\Admin\XPaymentsUserSubscription)
        $result->{SubscriptionRepo::SEARCH_PROFILE}
            = $this->getProfile();
        $result->{SubscriptionRepo::SEARCH_ORDER_BY} = $this->getOrderBy();

        return $result;
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $this->commonParams = parent::getCommonParams();
        $this->commonParams['profile_id'] = $this->getProfileId();

        return $this->commonParams;
    }

}
