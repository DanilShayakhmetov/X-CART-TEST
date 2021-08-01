<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Model;

use XLite\Model\Order\Status\Payment;
use XLite\Module\XPay\XPaymentsCloud\Main as XPaymentsCloud;
use XPaymentsCloud\ApiException;

/**
 * Cart
 */
class Cart extends \XLite\Model\Cart implements \XLite\Base\IDecorator
{
    /**
     * @return bool
     */
    public function hasXpaymentsSubscriptionItems()
    {
        $result = false;

        foreach ($this->getItems() as $item) {
            if ($item->isXpaymentsSubscription()) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Returns the list of session vars that must be cleared on logoff
     *
     * @return array
     */
    public function getSessionVarsToClearOnLogoff()
    {
        return parent::getSessionVarsToClearOnLogoff() + [
                'buy_with_apple_pay_order_id'
            ];
    }

}
