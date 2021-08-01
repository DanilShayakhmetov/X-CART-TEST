<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Module\XC\ThemeTweaker\Core\Notifications;

use XLite\Core\Database;
use XLite\Module\XC\Reviews\Model\Review;


/**
 * StaticProvider
 *
 * @Decorator\Depend("XC\ThemeTweaker")
 */
 class StaticProvider extends \XLite\Module\XC\VendorMessages\Module\XC\ThemeTweaker\Core\Notifications\StaticProvider implements \XLite\Base\IDecorator
{
    protected static function getNotificationsStaticData()
    {
        return parent::getNotificationsStaticData() + [
                'modules/XC/Reviews/new_review' => [
                    'review' => static::getDemoReview(),
                    'vendor'
                ],
            ];
    }

    /**
     * @return Review
     * @throws \Doctrine\ORM\ORMException
     */
    protected static function getDemoReview()
    {
        if ($product = Database::getRepo('XLite\Model\Product')->findDumpProduct()) {
            $review = new Review();
            $review->setAdditionDate(LC_START_TIME);
            $review->setProduct($product);
            $review->setProfile(
                Database::getRepo('XLite\Model\Profile')->findDumpProfile()
            );
            $review->setReview('Lorem ipsum dolor sit amet, consectetur adipisicing elit. A, ab architecto aut commodi consequatur delectus distinctio earum excepturi iusto laboriosam quaerat recusandae, repellendus ut, veritatis vitae? Ipsum iste nostrum saepe!');
            $review->setRating(3);
            $review->setReviewerName('name');

            return $review;
        }

        return null;
    }
}