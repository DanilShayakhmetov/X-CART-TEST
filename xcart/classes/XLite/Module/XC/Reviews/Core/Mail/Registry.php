<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Core\Mail;


class Registry extends \XLite\Core\Mail\Registry implements \XLite\Base\IDecorator
{
    protected static function getNotificationsList()
    {
        return array_merge_recursive(parent::getNotificationsList(), [
            \XLite::CUSTOMER_INTERFACE => [
                'modules/XC/Reviews/review_key' => OrderReviewKey::class,
            ],
            \XLite::ADMIN_INTERFACE    => [
                'modules/XC/Reviews/new_review' => NewReview::class,
            ],
        ]);
    }
}