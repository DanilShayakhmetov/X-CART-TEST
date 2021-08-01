<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Order;


use XLite\Core\Converter;
use XLite\Model\Order;

class TrackingCustomer extends \XLite\Core\Mail\Order\ACustomer
{
    static function getDir()
    {
        return 'order_tracking_information';
    }

    public function __construct(Order $order)
    {
        parent::__construct($order);
        $url = \XLite::getInstance()->getShopURL(Converter::buildURL(
            'order',
            '',
            ['order_number' => $order->getOrderNumber()],
            \XLite::getCustomerScript()
        ));
        $this->appendData([
            'trackingNumbers' => $order->getTrackingNumbers(),
            'orderURL'        => $url,
            'address'        => $order->getProfile()->getBillingAddress(),
        ]);
    }

    public function send()
    {
        $result = parent::send();

        if ($result) {
            if ($this->getOrder()) {
                \XLite\Core\OrderHistory::getInstance()->registerCustomerEmailSent(
                    $this->getOrder()->getOrderId(),
                    'Tracking information is sent to the customer'
                );
            }
        }

        return $result;
    }
}