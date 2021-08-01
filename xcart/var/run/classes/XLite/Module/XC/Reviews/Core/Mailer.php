<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Core;

use XLite\Module\XC\Reviews\Core\Mail\NewReview;
use XLite\Module\XC\Reviews\Core\Mail\OrderReviewKey;

/**
 * Mailer
 */
abstract class Mailer extends \XLite\Module\XC\ThemeTweaker\Core\Mailer implements \XLite\Base\IDecorator
{
    const NEW_REVIEW_NOTIFICATION     = 'modules/XC/Reviews/new_review';
    const NEW_REVIEW_KEY_NOTIFICATION = 'modules/XC/Reviews/review_key';

    /**
     * @param \XLite\Module\XC\Reviews\Model\Review $review Review
     */
    public static function sendNewReview(\XLite\Module\XC\Reviews\Model\Review $review)
    {
        (new NewReview($review))->schedule();
    }

    /**
     * Send order review key (follow up notification) to customer
     *
     * @param \XLite\Module\XC\Reviews\Model\OrderReviewKey $reviewKey Review key object
     */
    public static function sendOrderReviewKey($reviewKey)
    {
        (new OrderReviewKey($reviewKey))->schedule();
    }
}
