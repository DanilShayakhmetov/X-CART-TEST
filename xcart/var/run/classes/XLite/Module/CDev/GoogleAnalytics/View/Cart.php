<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\View;

/**
 * Abstract widget
 */
abstract class Cart extends \XLite\Module\XC\CrispWhiteSkin\View\Cart implements \XLite\Base\IDecorator
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (\XLite\Module\CDev\GoogleAnalytics\Main::isECommerceEnabled()) {
            $list[] = 'modules/CDev/GoogleAnalytics/universal/action/ecommerce/ga-ec-shopping-cart.js';
        }

        return $list;
    }
}
