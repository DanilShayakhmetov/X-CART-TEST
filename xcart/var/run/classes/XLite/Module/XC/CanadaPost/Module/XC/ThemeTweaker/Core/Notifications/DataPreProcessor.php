<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Module\XC\ThemeTweaker\Core\Notifications;

use XLite\Module\XC\CanadaPost\Model\ProductsReturn;
use XLite\Module\XC\CanadaPost\Model\ProductsReturn\Item as ReturnItem;

/**
 * DataPreProcessor
 *
 * @Decorator\Depend("XC\ThemeTweaker")
 */
 class DataPreProcessor extends \XLite\Module\XC\Reviews\Module\XC\ThemeTweaker\Core\Notifications\DataPreProcessor implements \XLite\Base\IDecorator
{
    /**
     * Prepare data to pass to constructor XLite\Module\XC\Reviews\Core\Mail\OrderReviewKey
     *
     * @param string $dir  Notification template directory
     * @param array  $data Data
     *
     * @return array
     */
    public static function prepareDataForNotification($dir, array $data)
    {
        $data = parent::prepareDataForNotification($dir, $data);

        if ($dir === 'modules/XC/CanadaPost/return_approved') {
            $data = [
                'return' => static::getDemoCanadaPostReturn($data['order'], true)
            ];
        }
        if ($dir === 'modules/XC/CanadaPost/return_rejected') {
            $data = [
                'return' => static::getDemoCanadaPostReturn($data['order'], false)
            ];
        }

        return $data;
    }

    /**
     * Get return for notification
     *
     * @param \XLite\Model\Order
     * @return ProductsReturn
     */
    protected static function getDemoCanadaPostReturn($order, $approved = true)
    {
        $return = null;

        if ($order) {
            $return = new ProductsReturn();
            $return->setDate(time());
            $return->setStatus($approved ? ProductsReturn::STATUS_APPROVED : ProductsReturn::STATUS_REJECTED);
            $return->setNotes('Lorem ipsum dolor sit amet, consectetur adipisicing elit. A, ab architecto aut commodi consequatur delectus distinctio earum excepturi iusto laboriosam quaerat recusandae, repellendus ut, veritatis vitae? Ipsum iste nostrum saepe!');
            $return->setAdminNotes('Lorem ipsum dolor sit amet, consectetur adipisicing elit. A, ab architecto aut commodi consequatur delectus distinctio earum excepturi iusto laboriosam quaerat recusandae, repellendus ut, veritatis vitae? Ipsum iste nostrum saepe!');
            $return->setOrder($order);

            foreach ($order->getItems() as $item) {
                $rItem = new ReturnItem();
                $rItem->setReturn($return);
                $rItem->setOrderItem($item);
                $rItem->setAmount($item->getAmount());

                $return->addItems($rItem);
            }

            return $return;
        }

        return $return;
    }
}
