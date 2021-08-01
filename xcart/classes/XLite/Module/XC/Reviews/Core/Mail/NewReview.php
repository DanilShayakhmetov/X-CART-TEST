<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Core\Mail;


use XLite\Core\Mailer;
use XLite\Module\XC\Reviews\Model\Review;

class NewReview extends \XLite\Core\Mail\AMail
{
    static function getInterface()
    {
        return \XLite::ADMIN_INTERFACE;
    }

    static function getDir()
    {
        return Mailer::NEW_REVIEW_NOTIFICATION;
    }

    protected static function defineVariables()
    {
        return array_merge(parent::defineVariables(), [
            'author_name'  => static::t('Username or e-mail'),
            'review'       => static::t('Text'),
            'product_name' => static::t('Product name'),
            'product_link' => \XLite::getInstance()->getShopURL(),
        ]);
    }

    public function __construct(Review $review)
    {
        parent::__construct();

        $replyTo = $review->getEmail() ?: '';

        if ($replyTo && $review->getReviewerName()) {
            $replyTo = [[
                'address' => $replyTo,
                'name'    => $review->getReviewerName(),
            ]];
        }

        $this->setFrom(Mailer::getOrdersDepartmentMail());
        $this->setTo(Mailer::getSiteAdministratorMails());
        $this->setReplyTo($replyTo);

        $this->populateVariables([
            'author_name'  => $review->getReviewerName() ?: $review->getEmail(),
            'review'       => $review->getReview(),
            'product_name' => $review->getProduct() ? $review->getProduct()->getName() : '',
            'product_link' => \XLite\Core\Converter::buildFullURL('product', '', [
                'product_id' => $review->getProduct()->getProductId(),
                'page'       => 'product_reviews',
            ], \XLite::getAdminScript()),
        ]);

        $this->appendData([
            'review' => $review,
            'rate'   => $review->getRating(),
        ]);
    }
}