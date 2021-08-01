<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\View\Form\Product;

/**
 * Add product to cart form
 */
class AddToCart extends \XLite\View\Form\Product\AddToCart implements \XLite\Base\IDecorator
{
    /**
     * getFormDefaultParams
     *
     * @return array
     */
    protected function getFormDefaultParams()
    {
        $list = parent::getFormDefaultParams();

        if (\XLite\Core\Request::getInstance()->ga_list) {
            $list['ga_list'] = \XLite\Core\Request::getInstance()->ga_list;
        }

        return $list;
    }

}
