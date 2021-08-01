<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Core\Mail;


use XLite\Core\Mailer;
use XLite\Model\Profile;
use XLite\Module\XC\Reviews\Model\Review;

class NewReviewVendor extends NewReview
{
    static function getInterface()
    {
        return \XLite::ADMIN_INTERFACE;
    }

    static function getDir()
    {
        return Mailer::NEW_REVIEW_NOTIFICATION;
    }

    public function __construct(Review $review, Profile $vendor)
    {
        parent::__construct($review);
        $this->setTo(['email' => $vendor->getLogin(), 'name' => $vendor->getName(false)]);
    }
}