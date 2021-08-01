<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\FormModel\Product;

/**
 * Product form model
 */
class Info extends \XLite\View\FormModel\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @return string
     */
    protected function getProductPreviewURL()
    {
        return parent::getProductPreviewURL() . '&activate_editor=1';
    }
}
