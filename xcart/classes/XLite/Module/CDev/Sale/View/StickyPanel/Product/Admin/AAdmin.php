<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\StickyPanel\Product\Admin;


/**
 * Abstract product panel for admin interface
 */
class AAdmin extends \XLite\View\StickyPanel\Product\Admin\AAdmin implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/CDev/Sale/sticky_panel/product_list/script.js'
        ]);
    }
}