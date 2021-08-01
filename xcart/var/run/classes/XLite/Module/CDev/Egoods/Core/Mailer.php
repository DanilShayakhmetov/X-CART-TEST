<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Core;

use XLite\Module\CDev\Egoods\Core\Mail\EgoodsLinkCustomer;

/**
 * Mailer
 */
abstract class Mailer extends \XLite\Module\XC\CanadaPost\Core\Mailer implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Model\Order $order Order model
     */
    public static function sendEgoodsLinks(\XLite\Model\Order $order)
    {
        static::sendEgoodsLinksCustomer($order);
    }

    /**
     * @param \XLite\Model\Order $order Order model
     */
    public static function sendEgoodsLinksCustomer(\XLite\Model\Order $order)
    {
        (new EgoodsLinkCustomer($order))->schedule();
    }
}
