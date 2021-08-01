<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View;

use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Model\Order;

/**
 * Popup payment additional info
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class PopupXpaymentsCards extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['popup_xpayments_cards']);
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XPay/XPaymentsCloud/order/xpayments_cards';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Return recharge amount
     *
     * @return string
     */
    protected function getAmount()
    {
        return $this->formatPrice(Request::getInstance()->amount);
    }

    /**
     * Return customer's saved credit cards
     *
     * @return array
     */
    protected function getCards()
    {
        /** @var Order $order */
        $order = Database::getRepo('XLite\Model\Order')->findOneByOrderNumber(
            Request::getInstance()->order_number
        );

        $cards = [];

        if ($order) {
            $cards = $order->getActiveXpaymentsCards();
        }

        return $cards;
    }

}
