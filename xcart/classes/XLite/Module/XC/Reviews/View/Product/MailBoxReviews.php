<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Product;

class MailBoxReviews extends \XLite\View\Product\MailBox
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'modules/XC/Reviews/product/mailbox/style.less',
        ]);
    }

    /**
     * @return bool
     */
    protected function isDisplayPrice()
    {
        return false;
    }

    /**
     * @return bool
     */
    protected function isDisplayAddReviewButton()
    {
        return true;
    }

    /**
     * @return string
     */
    protected function getAddReviewURL()
    {
        return $this->getProductURL() . '#product-details-tab-reviews';
    }
}
