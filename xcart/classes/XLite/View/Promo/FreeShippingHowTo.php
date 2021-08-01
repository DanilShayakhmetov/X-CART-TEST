<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Promo;


/**
 * FreeShippingHowTo
 *
 * @ListChild (list="carriers.before", zone="admin")
 */
class FreeShippingHowTo extends \XLite\View\AView
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'shipping/promo.twig';
    }

    /**
     * @return string
     */
    public function getFreeShippingHowToText()
    {
        return static::t('How to set up free shipping help', ['url' => static::t('https://kb.x-cart.com/shipping/free_shipping.html')]);
    }
}