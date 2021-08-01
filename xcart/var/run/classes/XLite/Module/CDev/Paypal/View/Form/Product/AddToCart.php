<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Form\Product;

/**
 * Place order form
 */
 class AddToCart extends \XLite\Module\XC\CustomerAttachments\View\Form\Product\AddToCart implements \XLite\Base\IDecorator
{
    /**
     * getFormDefaultParams
     *
     * @return array
     */
    protected function getFormDefaultParams()
    {
        $list = parent::getFormDefaultParams();

        if (\XLite\Module\CDev\Paypal\Main::isExpressCheckoutEnabled()) {
            $list['expressCheckout'] = false;
            $list['inContext'] = true;
            $list['cancelUrl'] = $this->isAjax()
                ? $this->getReferrerURL()
                : \XLite\Core\URLManager::getSelfURI();
        }

        return $list;
    }
}
