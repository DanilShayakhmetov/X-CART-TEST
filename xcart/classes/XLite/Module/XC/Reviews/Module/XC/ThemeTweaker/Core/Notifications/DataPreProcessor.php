<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Module\XC\ThemeTweaker\Core\Notifications;

use XLite\Module\XC\Reviews\Model\OrderReviewKey;

/**
 * DataPreProcessor
 *
 * @Decorator\Depend("XC\ThemeTweaker")
 */
class DataPreProcessor extends \XLite\Module\XC\ThemeTweaker\Core\Notifications\DataPreProcessor implements \XLite\Base\IDecorator
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

        if ($dir === 'modules/XC/Reviews/review_key') {
            $data = [
                'review_key' => static::getDemoOrderReviewKey($data['order'])
            ];
        }

        return $data;
    }

    /**
     * Get order review key for notification
     *
     * @param Order
     * @return OrderReviewKey
     */
    protected static function getDemoOrderReviewKey($order)
    {
        $key = null;

        if ($order) {
            $key = new OrderReviewKey();
            $key->setKeyValue('review_key');
            $key->setAddedDate(LC_START_TIME);
            $key->setSentDate(0);
            $key->setOrder($order);
        }

        return $key;
    }
}
