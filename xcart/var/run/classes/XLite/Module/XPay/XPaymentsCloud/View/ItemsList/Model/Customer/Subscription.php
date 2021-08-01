<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\ItemsList\Model\Customer;

use XLite\Module\XPay\XPaymentsCloud\Model\Repo\Subscription\Subscription as SubscriptionRepo;

/**
 * Account pin codes based on orders
 *
 */
class Subscription extends \XLite\View\ItemsList\AItemsList
{
    /**
     * Define widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XPay/XPaymentsCloud';
    }

    /**
     * Define page body templates directory
     *
     * @return string
     */
    protected function getPageBodyDir()
    {
        return 'subscription';
    }

    /**
     * @return string
     */
    protected function getListHead()
    {
        return $this->getItemsCount()
            ? static::t('X subscriptions', ['count' => $this->getItemsCount()])
            : static::t('No subscriptions');
    }

    /**
     * @return boolean
     */
    protected function isHeadVisible()
    {
        return true;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return '\XLite\Module\XPay\XPaymentsCloud\View\Pager\Customer\Subscription';
    }

    /**
     * Check if pager is visible
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return $this->hasResults();
    }

    /**
     * isEmptyListTemplateVisible
     *
     * @return string
     */
    protected function isEmptyListTemplateVisible()
    {
        return false;
    }

    /**
     * Auxiliary method to check visibility
     *
     * @return boolean
     */
    protected function isDisplayWithEmptyList()
    {
        return true;
    }

    /**
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd Search condition
     * @param boolean $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $cnd->{SubscriptionRepo::SEARCH_PROFILE} = \XLite\Core\Auth::getInstance()->getProfile();
        $cnd->{SubscriptionRepo::SEARCH_ORDER_BY} = ['s.id', 'DESC'];

        return \XLite\Core\Database::getRepo('\XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription')
            ->search($cnd, $countOnly);
    }

}
