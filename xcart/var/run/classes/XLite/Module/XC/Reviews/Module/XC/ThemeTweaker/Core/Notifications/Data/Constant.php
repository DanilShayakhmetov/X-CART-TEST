<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Module\XC\ThemeTweaker\Core\Notifications\Data;


/**
 * Constant
 *
 * @Decorator\Depend("XC\ThemeTweaker")
 */
 class Constant extends \XLite\Module\XC\ThemeTweaker\Core\Notifications\Data\ConstantAbstract implements \XLite\Base\IDecorator
{
    public function isAvailable($templateDir)
    {
        return parent::isAvailable($templateDir) && (
                $templateDir !== 'modules/XC/Reviews/new_review'
                || $this->getName($templateDir) !== 'review'
                || $this->getData($templateDir)
            );
    }

}