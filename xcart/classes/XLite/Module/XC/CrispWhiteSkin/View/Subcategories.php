<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View;

/**
 * Subcategories list
 */
class Subcategories extends \XLite\View\Subcategories implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (!\XLite::isAdminZone()) {
            $list[] = 'subcategories/subcategories.js';
        }

        return $list;
    }
}
