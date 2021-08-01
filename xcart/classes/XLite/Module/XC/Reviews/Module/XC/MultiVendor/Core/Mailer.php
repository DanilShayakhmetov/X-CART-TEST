<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Module\XC\MultiVendor\Core;

use XLite\Module\XC\Reviews\Core\Mail\NewReviewVendor;

/**
 * Mailer
 * 
 * @Decorator\Depend ({"XC\Reviews","XC\MultiVendor"})
 */
abstract class Mailer extends \XLite\Core\Mailer implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Module\XC\Reviews\Model\Review $review Review
     */
    public static function sendNewReview(\XLite\Module\XC\Reviews\Model\Review $review)
    {
        if ($review->getProduct()->getVendor()) {
            static::sendNewReviewVendor(
                $review->getProduct()->getVendor(),
                $review
            );
        } else {
            static::sendNewReviewAdmin($review);
        }
    }

    /**
     * @param \XLite\Module\XC\Reviews\Model\Review $review Review
     */
    public static function sendNewReviewVendor(\XLite\Model\Profile $vendor, \XLite\Module\XC\Reviews\Model\Review $review)
    {
        (new NewReviewVendor($review, $vendor))->schedule();
    }

    /**
     * @param \XLite\Module\XC\Reviews\Model\Review $review Review
     */
    public static function sendNewReviewAdmin(\XLite\Module\XC\Reviews\Model\Review $review)
    {
        parent::sendNewReview($review);
    }
}
