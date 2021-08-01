<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Core\Task;

use XLite\Model\OrderItem;

/**
 * Send order review keys (follow ups)
 */
class SendReviewKeys extends \XLite\Core\Task\Base\Periodic
{
    /**
     * The maximum number of orders processed per step.
     */
    const MAX_NOTIFICATIONS_PER_STEP = 10;

    /**
     * Return title for the task
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Send review requests to customers';
    }

    /**
     * Return the delay (in seconds) between performing task steps.
     *
     * @return integer
     */
    protected function getPeriod()
    {
        return static::INT_15_MIN;
    }

    /**
     * Run a task step.
     *
     * @return void
     */
    protected function runStep()
    {
        $reviewKeys = $this->getReviewKeys();

        foreach ($reviewKeys as $rKey) {
            $this->sendReviewKey($rKey);
        }
    }

    /**
     * Get list of review keys available to be sent
     *
     * @return array
     */
    protected function getReviewKeys()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\Reviews\Model\OrderReviewKey')->findValidReviewKeys(static::MAX_NOTIFICATIONS_PER_STEP);
    }

    /**
     * Send review key
     *
     * @param \XLite\Module\XC\Reviews\Model\OrderReviewKey $reviewKey
     */
    protected function sendReviewKey($reviewKey)
    {
        $items = $reviewKey->getOrder()->getItems()->toArray();

        $hasReviewAbleProducts = (boolean)array_filter(array_map(function (OrderItem $item) {
            return $this->isItemAvailableForReview($item);
        }, $items));

        if ($hasReviewAbleProducts) {
            \XLite\Core\Mailer::sendOrderReviewKey($reviewKey);
            $reviewKey->setSentDate(\XLite\Core\Converter::time());
        } else {
            // Order without products - remove review key

            if ($order = $reviewKey->getOrder()) {
                $order->setReviewKey(null);
            }

            \XLite\Core\Database::getEM()->remove($reviewKey);
        }
    }

    /**
     * Return true if order item is available for product review
     *
     * @param \XLite\Model\OrderItem $item OrderItem model object
     *
     * @return boolean
     */
    protected function isItemAvailableForReview($item)
    {
        return !$item->isDeleted() && $item->getProduct()->isAvailable();
    }
}
