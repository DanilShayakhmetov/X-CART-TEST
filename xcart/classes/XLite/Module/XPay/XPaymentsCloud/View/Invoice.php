<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View;

use XLite\Model\OrderItem;

/**
 * Invoice page
 */
class Invoice extends \XLite\View\Invoice implements \XLite\Base\IDecorator
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XPay/XPaymentsCloud/invoice/style.css';
        $list[] = 'modules/XPay/XPaymentsCloud/order/invoice/style.css';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list['css'][] = 'modules/XPay/XPaymentsCloud/account/cc_type_sprites.css';

        return $list;
    }

    /**
     * Is next payment date available for current order
     *
     * @param OrderItem $item Order item
     *
     * @return boolean
     */
    protected function isXpaymentsNextPaymentDateAvailable(OrderItem $item)
    {
        return $item->isXpaymentsNextPaymentDateAvailable();
    }

    /**
     * Is last payment failed for current subscription
     *
     * @param OrderItem $item Order item
     *
     * @return boolean
     */
    protected function isXpaymentsLastPaymentFailed($item)
    {
        /** @var \XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription $subscription */
        $subscription = $item->getXpaymentsSubscription();

        return $subscription
            && $subscription->getActualDate() > $subscription->getPlannedDate();
    }

    /**
     * Get next payment date
     *
     * @param \XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription $subscription Subscription
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
     * @param \XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription $subscription Subscription
     *
     * @return integer
     */
    protected function getNextAattemptDate($subscription)
    {
        return $subscription->getActualDate();
    }

}
