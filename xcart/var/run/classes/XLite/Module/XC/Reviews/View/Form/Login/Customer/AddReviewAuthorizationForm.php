<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Form\Login\Customer;

/**
 * Customer lig-in form
 */
class AddReviewAuthorizationForm extends \XLite\View\Form\Login\Customer\Main
{
    /**
     * Required form parameters
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        $list = parent::getCommonFormParams();

        if (\XLite\Core\Request::getInstance()->fromURL) {
            $url = \XLite\Core\Request::getInstance()->fromURL;
        } else {
            $url = $this->buildURL('product', '', [
                    'product_id' => \XLite\Core\Request::getInstance()->product_id
                ]);
            $url .= '#product-details-tab-reviews';
        }

        $list['referer'] = $url;

        return $list;
    }
}