<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Pager\Customer\Product;

abstract class AProduct extends \XLite\View\Pager\Customer\Product\AProductAbstract implements \XLite\Base\IDecorator
{
    protected function getPerPageCounts()
    {
        $min = \XLite\Core\Config::getInstance()->General->products_per_page;
        $max = \XLite\Core\Config::getInstance()->General->products_per_page_max;

        // Yeah..
        $min = min($min, $max);
        $base = 2;

        return array_map(function ($item) use ($min, $base) {
            return floor(pow($base, $item) * $min);
        }, range(0, ceil(log($max / $min, $base)) - 1));
    }
}
