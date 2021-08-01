<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\Core;

/**
 * Mailer
 */
abstract class Mailer extends \XLite\Core\Mailer implements \XLite\Base\IDecorator
{
    /**
     * Send update Amazon payment info notification to customer
     *
     * @param \XLite\Model\Order $order Order object
     *
     * @return string
     */
    public static function sendUpdateAmazonPaymentInfo($order)
    {
        (new \XLite\Module\Amazon\PayWithAmazon\Core\Mail\UpdateAmazonPaymentInfo($order))->schedule();
    }
}
