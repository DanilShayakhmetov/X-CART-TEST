<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\ProductTags\Module\XC\ProductTags\View\FormField\Inline\Input;

/**
 * Class Text
 * @Decorator\Depend("XC\MultiVendor")
 */
class Text extends \XLite\Module\XC\ProductTags\View\FormField\Inline\Input\Text implements \XLite\Base\IDecorator
{
    protected function isEditable()
    {
        return parent::isEditable() && ($this->getEditOnly() || !\XLite\Core\Auth::getInstance()->isVendor());
    }
}